<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::customers()
            ->withCount('orders')
            ->latest()
            ->paginate(20);

        $counts = [
            'total' => User::customers()->count(),
            'active' => User::customers()->where('status', 'active')->count(),
            'inactive' => User::customers()->where('status', 'inactive')->count(),
            'suspended' => User::customers()->where('status', 'suspended')->count(),
        ];

        return view('admin.customers.index', compact('customers', 'counts'));
    }

    public function show(User $customer)
    {
        $this->ensureCustomer($customer);

        $customer->load([
            'orders.payment',
            'orders.vendorOrders.vendor',
        ]);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $this->ensureCustomer($customer);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone_number' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone_number')->ignore($customer->id)],
            'status' => 'required|in:active,inactive,suspended',
            'is_active' => 'nullable|boolean',
            'email_verified' => 'nullable|boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($customer, $validated, $request) {
            $customer->name = $validated['name'];
            $customer->email = $validated['email'];
            $customer->phone_number = $validated['phone_number'] ?? null;
            $customer->status = $validated['status'];
            $customer->is_active = $request->boolean('is_active');
            $customer->email_verified_at = $request->boolean('email_verified') ? ($customer->email_verified_at ?? now()) : null;

            if (!empty($validated['password'])) {
                $customer->password = $validated['password'];
            }

            $customer->save();
        });

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer information updated successfully.');
    }

    public function destroy(User $customer)
    {
        $this->ensureCustomer($customer);

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer account deleted successfully.');
    }

    private function ensureCustomer(User $customer): void
    {
        abort_unless($customer->isCustomer(), 404);
    }
}
