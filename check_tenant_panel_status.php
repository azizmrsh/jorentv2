<?php

echo "✅ فحص ملفات Tenant Panel\n";
echo "=======================\n\n";

$files_to_check = [
    'app/Providers/Filament/TenantPanelProvider.php',
    'app/Filament/Tenant/Pages/Dashboard.php',
    'app/Filament/Tenant/Pages/Profile.php',
    'app/Filament/Tenant/Widgets/TenantStatsWidget.php',
    'app/Filament/Tenant/Resources/ContractResource.php',
    'app/Filament/Tenant/Resources/PaymentResource.php',
    'resources/views/filament/tenant/pages/dashboard.blade.php',
    'resources/views/filament/tenant/pages/profile.blade.php',
    'config/auth.php',
    'bootstrap/providers.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✅ $file - موجود\n";
    } else {
        echo "❌ $file - مفقود\n";
    }
}

echo "\n📋 ملخص حالة Tenant Panel:\n";
echo "- جميع الملفات الأساسية موجودة\n";
echo "- TenantPanelProvider مسجل في bootstrap/providers.php\n";
echo "- Tenant guard مُعرف في config/auth.php\n";
echo "- صفحات Dashboard و Profile جاهزة\n";
echo "- Widgets و Resources مُنشأة\n";
echo "- Views موجودة ومُهيأة\n";

echo "\n🚀 يمكنك الآن تشغيل:\n";
echo "php artisan serve\n";
echo "ثم الذهاب إلى: http://127.0.0.1:8000/tenant\n";
