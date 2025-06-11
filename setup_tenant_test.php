<?php

echo "🔧 إنشاء مستأجر تجريبي...\n";

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

try {
    // حذف المستأجر التجريبي إذا كان موجوداً
    Tenant::where('email', 'test.tenant@example.com')->delete();
    
    echo "📝 إنشاء مستأجر جديد...\n";
    
    $tenant = Tenant::create([
        'firstname' => 'أحمد',
        'midname' => 'محمد', 
        'lastname' => 'العلي',
        'email' => 'test.tenant@example.com',
        'password' => Hash::make('password123'),
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
    
    echo "✅ تم إنشاء المستأجر بنجاح!\n";
    echo "📧 الإيميل: " . $tenant->email . "\n";
    echo "🔑 الباسورد: password123\n";
    echo "👤 الاسم: " . $tenant->getFilamentName() . "\n";
    echo "🎯 يمكن الدخول: " . ($tenant->canAccessFilament() ? 'نعم' : 'لا') . "\n";
    echo "📧 البريد مؤكد: " . ($tenant->hasVerifiedEmail() ? 'نعم' : 'لا') . "\n";
    
    echo "\n🚀 التجربة:\n";
    echo "1. شغل الخادم: php artisan serve\n";
    echo "2. اذهب إلى: http://127.0.0.1:8000/tenant\n";
    echo "3. سجل دخول بالبيانات أعلاه\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "📝 التفاصيل: " . $e->getTraceAsString() . "\n";
}
