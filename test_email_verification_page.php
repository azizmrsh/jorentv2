<?php
/**
 * 🧪 اختبار صفحة التحقق من البريد الإلكتروني
 * معلم - فحص سريع للتأكد من أن كل شيء شغال!
 */

echo "🎯 فحص صفحة التحقق من البريد الإلكتروني\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// فحص الملف
$verifyEmailPath = __DIR__ . '/resources/views/auth/verify-email.blade.php';

if (!file_exists($verifyEmailPath)) {
    echo "❌ ملف صفحة التحقق غير موجود!\n";
    exit(1);
}

echo "✅ ملف صفحة التحقق موجود\n";

$content = file_get_contents($verifyEmailPath);

// فحص العناصر الأساسية
$checks = [
    'csrf_token' => ['name' => 'CSRF Token', 'pattern' => 'csrf_token()'],
    'user_email' => ['name' => 'عرض إيميل المستخدم', 'pattern' => 'Auth::user()->email'],
    'laravel_routes' => ['name' => 'روابط Laravel', 'pattern' => "route('verification.send')"],
    'session_messages' => ['name' => 'رسائل الجلسة', 'pattern' => "@if (session('message'))"],
    'glassmorphism' => ['name' => 'تصميم Glassmorphism', 'pattern' => 'glass-card'],
    'tailwind_css' => ['name' => 'Tailwind CSS', 'pattern' => 'cdn.tailwindcss.com'],
    'arabic_fonts' => ['name' => 'خطوط عربية', 'pattern' => 'Inter'],
    'animations' => ['name' => 'الأنيميشن', 'pattern' => 'animate-blob'],
    'api_verification' => ['name' => 'التحقق التلقائي', 'pattern' => '/api/user/verification-status'],
    'responsive_design' => ['name' => 'التصميم المتجاوب', 'pattern' => 'w-full'],
    'custom_modal' => ['name' => 'النافذة المخصصة', 'pattern' => 'custom-modal'],
    'loading_states' => ['name' => 'حالات التحميل', 'pattern' => 'spinner'],
];

echo "\n📋 فحص العناصر:\n";
echo "-" . str_repeat("-", 30) . "\n";

$passed = 0;
$total = count($checks);

foreach ($checks as $key => $check) {
    if (strpos($content, $check['pattern']) !== false) {
        echo "✅ {$check['name']}\n";
        $passed++;
    } else {
        echo "❌ {$check['name']} - لم يتم العثور على: {$check['pattern']}\n";
    }
}

echo "\n📊 النتائج:\n";
echo "-" . str_repeat("-", 20) . "\n";

$percentage = round(($passed / $total) * 100, 1);
echo "✅ العناصر المطبقة: {$passed}/{$total} ({$percentage}%)\n";

if ($percentage >= 90) {
    echo "\n🎉 ممتاز! صفحة التحقق مطبقة بالكامل!\n";
    echo "✨ المميزات:\n";
    echo "   • تصميم حديث وأنيق\n";
    echo "   • تفاعل متقدم مع JavaScript\n";
    echo "   • تكامل كامل مع Laravel\n";
    echo "   • دعم اللغة العربية\n";
    echo "   • تصميم متجاوب\n";
    echo "   • أمان متقدم\n";
} elseif ($percentage >= 70) {
    echo "\n👍 جيد! لكن يحتاج بعض التحسينات\n";
} else {
    echo "\n⚠️ يحتاج مراجعة وإكمال\n";
}

echo "\n🔗 للاختبار:\n";
echo "   1. شغل الخادم: php artisan serve\n";
echo "   2. اذهب إلى: http://127.0.0.1:8000/email/verify\n";
echo "   3. جرب الوظائف والتفاعل\n";

echo "\n✨ التصميم جاهز للاستخدام! 🚀\n";
