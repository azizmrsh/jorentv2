<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix permission tables migration status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // إضافة المايجريشن إلى قاعدة البيانات
            DB::table('migrations')->insert([
                'migration' => '2025_06_11_140459_create_permission_tables',
                'batch' => 9
            ]);
            
            $this->info('تم إصلاح حالة المايجريشن بنجاح!');
            
            // التحقق من وجود حساب Super Admin
            $admin = \App\Models\User::where('email', 'admin@admin.com')->first();
            if ($admin) {
                $this->info('حساب Super Admin موجود: ' . $admin->email);
                
                // التحقق من الأدوار
                $roles = $admin->getRoleNames();
                $this->info('الأدوار: ' . $roles->implode(', '));
            } else {
                $this->error('حساب Super Admin غير موجود!');
            }
            
        } catch (\Exception $e) {
            $this->error('خطأ: ' . $e->getMessage());
        }
    }
}
