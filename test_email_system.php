<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 اختبار نظام البريد الإلكتروني\n";
echo str_repeat("=", 50) . "\n\n";

// فحص الإعدادات
echo "📧 إعدادات البريد:\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM: " . config('mail.from.address') . "\n\n";

// اختبار إرسال بريد
echo "📨 اختبار إرسال البريد...\n";

try {
    \Illuminate\Support\Facades\Mail::raw('🧪 اختبار البريد - ' . date('Y-m-d H:i:s'), function($message) {
        $message->to('osaidalhajj03@gmail.com')
                ->subject('🧪 اختبار البريد - JoRent');
    });
    
    echo "✅ تم إرسال الإيميل بنجاح!\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في الإرسال: " . $e->getMessage() . "\n";
    echo "📄 الملف: " . $e->getFile() . "\n";
    echo "📍 السطر: " . $e->getLine() . "\n";
}

echo "\n🔍 اختبار التحقق من البريد للمستخدم...\n";

// اختبار إشعار التحقق للمستخدم
try {
    $user = \App\Models\User::first();
    if ($user) {
        echo "👤 المستخدم: {$user->name} ({$user->email})\n";
        echo "✅ التحقق: " . ($user->hasVerifiedEmail() ? "مؤكد" : "غير مؤكد") . "\n";
        
        if (!$user->hasVerifiedEmail()) {
            echo "📧 إرسال رابط التحقق...\n";
            $user->sendEmailVerificationNotification();
            echo "✅ تم إرسال رابط التحقق!\n";
        }
    } else {
        echo "❌ لا يوجد مستخدمين في النظام\n";
    }
} catch (Exception $e) {
    echo "❌ خطأ في التحقق: " . $e->getMessage() . "\n";
}

echo "\n✨ انتهى الاختبار\n";
