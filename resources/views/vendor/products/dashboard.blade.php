@extends('layouts.vendor')

@section('title', 'Products Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Products Dashboard</h1>
        <p class="text-gray-600 mt-1">Manage and track your product performance</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Products</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_products'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Published</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Sales</p>
            <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['total_sales'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Revenue</p>
            <p class="text-3xl font-bold text-indigo-600 mt-2">₨{{ number_format($stats['total_revenue']) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Low Stock</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['low_stock'] }}</p>
        </div>
    </div>

    <!-- Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Best Sellers -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Top Selling Products</h2>
            <div class="space-y-4">
                @forelse($topSelling as $product)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded">
                    <div>
                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-gray-600">{{ $product->sales_count }} sales</p>
                    </div>
                    <p class="font-bold text-gray-900">₨{{ number_format($product->total_revenue) }}</p>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">No sales yet</p>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alert -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Low Stock Alert</h2>
            <div class="space-y-4">
                @forelse($lowStockProducts as $product)
                <div class="flex items-center justify-between p-4 bg-red-50 rounded border border-red-200">
                    <div>
                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-red-600">{{ $product->getTotalStock() }} in stock</p>
                    </div>
                    <a href="{{ route('vendor.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">
                        Restock
                    </a>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">All products well stocked</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Products -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b">
            <h2 class="text-xl font-bold text-gray-900">Recent Products</h2>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Sales</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Created</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentProducts as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $product->name }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-900">{{ $product->sales_count ?? 0 }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->created_at->format('M d, Y') }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('vendor.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        No products yet. <a href="{{ route('vendor.products.create') }}" class="text-indigo-600 hover:text-indigo-900">Create one now</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection