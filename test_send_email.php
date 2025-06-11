<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Mail;

echo "=== اختبار إرسال البريد الإلكتروني ===\n\n";

try {
    // البحث عن أول مستخدم
    $user = User::first();
    
    if (!$user) {
        echo "❌ لا يوجد مستخدمين في قاعدة البيانات\n";
        exit;
    }
    
    echo "👤 المستخدم: {$user->name} ({$user->email})\n";
    echo "📧 حالة التحقق: " . ($user->hasVerifiedEmail() ? "مؤكد ✅" : "غير مؤكد ❌") . "\n\n";
    
    echo "🔄 جاري إرسال رابط التحقق...\n";
    
    // إرسال بريد التحقق
    $user->sendEmailVerificationNotification();
    
    echo "✅ تم إرسال رابط التحقق بنجاح!\n";
    echo "📧 تحقق من البريد الإلكتروني: {$user->email}\n";
    echo "📁 لا تنس التحقق من مجلد Spam/Junk\n";
    
} catch (\Exception $e) {
    echo "❌ خطأ في إرسال البريد:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\n=== انتهى الاختبار ===\n";
