<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

echo "🔧 فحص نظام المستأجرين...\n";

try {
    // التحقق من المستأجر التجريبي
    $tenant = Tenant::where('email', 'tenant@test.com')->first();
    
    if (!$tenant) {
        echo "📝 إنشاء مستأجر تجريبي...\n";
        $tenant = Tenant::create([
            'firstname' => 'أحمد',
            'midname' => 'محمد',
            'lastname' => 'العلي',
            'email' => 'tenant@test.com',
            'password' => Hash::make('password'),
            'phone' => '+962791234567',
            'address' => 'عمان، الأردن',
            'birth_date' => '1990-01-01',
            'status' => 'active',
            'document_type' => 'id',
            'document_number' => '1234567890',
            'nationality' => 'أردني',
            'hired_date' => now(),
            'hired_by' => 'نظام التجربة',
            'email_verified_at' => now(),
        ]);
    }
    
    echo "✅ المستأجر موجود: {$tenant->email}\n";
    echo "📛 الاسم: {$tenant->getFilamentName()}\n";
    echo "🔑 يمكن الدخول: " . ($tenant->canAccessFilament() ? 'نعم' : 'لا') . "\n";
    echo "✉️ البريد مؤكد: " . ($tenant->hasVerifiedEmail() ? 'نعم' : 'لا') . "\n";
    
    echo "\n🚀 النظام جاهز للتجربة!\n";
    echo "الرابط: http://127.0.0.1:8000/tenant\n";
    echo "الإيميل: tenant@test.com\n";
    echo "الباسورد: password\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
}

echo "\n⚡ لتشغيل الخادم: php artisan serve\n";
