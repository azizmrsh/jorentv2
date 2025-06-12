<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء دور super_admin
        $superAdminRole = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web'
        ]);

        // إعطاء جميع الأذونات للسوبر أدمن
        $superAdminRole->givePermissionTo(Permission::all());

        // البحث عن مستخدم موجود أو إنشاء واحد جديد
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super',
                'lastname' => 'Admin',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'status' => 'active'
            ]
        );

        // إعطاء الدور للمستخدم
        $superAdmin->assignRole($superAdminRole);

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@admin.com');
        $this->command->info('Password: password');
    }
}
