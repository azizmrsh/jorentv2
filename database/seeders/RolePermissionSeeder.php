<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $acc = Role::firstOrCreate(['name' => 'acc']);
        $tenant = Role::firstOrCreate(['name' => 'tenant']);

        // Basic permissions via Filament Shield
        if (class_exists('\\BezhanSalleh\\FilamentShield\\Commands\\MakeShieldPermissionsCommand')) {
            \Artisan::call('shield:install --all');
        }

        // Tenants only view their own data
        // Additional permission assignment logic can be added here
        // Admin role gets all permissions
        $admin->givePermissionTo(Permission::all());

        // acc role gets all except role management
        $acc->givePermissionTo(Permission::where('name', 'not like', '%Role%')->get());
    }
}
