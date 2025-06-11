<?php

echo "🔄 بدء العملية...\n";

try {
    require_once 'vendor/autoload.php';
    echo "✅ تم تحميل autoload\n";
    
    $app = require_once 'bootstrap/app.php';
    echo "✅ تم تحميل التطبيق\n";
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "✅ تم تهيئة النظام\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في التهيئة: " . $e->getMessage() . "\n";
    exit(1);
}

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

echo "إنشاء مستأجر للتجربة...\n";

try {
    echo "🔍 البحث عن مستأجر موجود...\n";
    // التحقق من وجود المستأجر أولاً
    $existingTenant = Tenant::where('email', 'tenant@test.com')->first();
    
    if ($existingTenant) {
        echo "✅ المستأجر موجود مسبقاً: " . $existingTenant->email . "\n";
        echo "الاسم: " . $existingTenant->full_name . "\n";
        echo "الحالة: " . $existingTenant->status . "\n";
    } else {
        echo "📝 إنشاء مستأجر جديد...\n";
        $tenant = Tenant::create([
            'firstname' => 'أحمد',
            'midname' => 'علي',
            'lastname' => 'محمد',
            'email' => 'tenant@test.com',
            'password' => Hash::make('password'),
            'status' => 'active',
            'email_verified_at' => now(),
            'phone' => '+962791234567',
            'address' => 'عمان، الأردن',
            'birth_date' => '1990-01-01',
            'nationality' => 'أردني',
            'hired_date' => now(),
            'hired_by' => 'الإدارة',
        ]);

        echo "✅ تم إنشاء المستأجر بنجاح!\n";
        echo "الإيميل: " . $tenant->email . "\n";
        echo "الاسم: " . $tenant->full_name . "\n";
        echo "الحالة: " . $tenant->status . "\n";
    }
    
    echo "\n";
    echo "🔐 معلومات الدخول:\n";
    echo "الرابط: http://localhost:8000/tenant\n";
    echo "الإيميل: tenant@test.com\n";
    echo "الباسورد: password\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
