<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Admin
            'manage_users'      => 'Manage system users',
            'manage_vendors'    => 'Approve, reject and manage vendors',
            'manage_categories' => 'Manage categories',
            'manage_products'   => 'Manage all products',
            'manage_orders'     => 'Manage all orders',
            'manage_payments'   => 'Manage payments and payouts',
            'manage_disputes'   => 'Manage disputes',
            'view_reports'      => 'View platform reports',
            'manage_settings'   => 'Manage system settings',

            // Vendor
            'create_product'   => 'Create products',
            'edit_product'     => 'Edit own products',
            'delete_product'   => 'Delete own products',
            'manage_inventory' => 'Manage own inventory',
            'view_analytics'  => 'View own analytics',
            'manage_payouts'  => 'Request and view own payouts',

            // Customer
            'browse_products' => 'Browse products',
            'make_purchase'   => 'Make purchases',
            'review_product'  => 'Review purchased products',
            'edit_profile'    => 'Edit own profile',

            // Shared
            'view_orders' => 'View own orders',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
                [
                    'name' => $name,
                    'guard_name' => 'web',
                ],
                [
                    'description' => $description,
                ]
            );
        }

        $admin = Role::updateOrCreate(
            [
                'name' => 'admin',
                'guard_name' => 'web',
            ],
            [
                'description' => 'Platform administrator',
            ]
        );

        $vendor = Role::updateOrCreate(
            [
                'name' => 'vendor',
                'guard_name' => 'web',
            ],
            [
                'description' => 'Seller / store owner',
            ]
        );

        $customer = Role::updateOrCreate(
            [
                'name' => 'customer',
                'guard_name' => 'web',
            ],
            [
                'description' => 'Buyer',
            ]
        );

        $admin->syncPermissions(Permission::all());

        $vendor->syncPermissions([
            'create_product',
            'edit_product',
            'delete_product',
            'manage_inventory',
            'view_analytics',
            'manage_payouts',
            'view_orders',
            'edit_profile',
        ]);

        $customer->syncPermissions([
            'browse_products',
            'make_purchase',
            'review_product',
            'edit_profile',
            'view_orders',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info(
            'Roles and permissions seeded: '
            . Role::count()
            . ' roles, '
            . Permission::count()
            . ' permissions.'
        );
    }
}