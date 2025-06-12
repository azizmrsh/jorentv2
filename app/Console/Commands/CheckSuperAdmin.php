<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CheckSuperAdmin extends Command
{
    protected $signature = 'check:superadmin';
    protected $description = 'Check if super admin exists and create if needed';

    public function handle()
    {
        $this->info('فحص وجود Super Admin...');
        
        // البحث عن المستخدم
        $user = User::where('email', 'admin@admin.com')->first();
        
        if ($user) {
            $this->info('✅ المستخدم موجود:');
            $this->info('الاسم: ' . $user->name . ' ' . $user->lastname);
            $this->info('الإيميل: ' . $user->email);
            $this->info('الأدوار: ' . $user->roles->pluck('name')->implode(', '));
            $this->info('عدد الأذونات: ' . $user->getAllPermissions()->count());
        } else {
            $this->warn('❌ المستخدم غير موجود. سيتم إنشاؤه...');
            
            // إنشاء دور super_admin
            $superAdminRole = Role::firstOrCreate([
                'name' => 'super_admin',
                'guard_name' => 'web'
            ]);

            // إعطاء جميع الأذونات للدور
            $superAdminRole->givePermissionTo(Permission::all());

            // إنشاء المستخدم
            $user = User::create([
                'name' => 'Super',
                'lastname' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'status' => 'active'
            ]);

            // إعطاء الدور للمستخدم
            $user->assignRole($superAdminRole);

            $this->info('✅ تم إنشاء Super Admin بنجاح!');
            $this->info('الإيميل: admin@admin.com');
            $this->info('كلمة المرور: password');
        }

        // عرض إجمالي المستخدمين
        $this->info('إجمالي المستخدمين: ' . User::count());
        $this->info('إجمالي الأدوار: ' . Role::count());
        $this->info('إجمالي الأذونات: ' . Permission::count());
    }
}
