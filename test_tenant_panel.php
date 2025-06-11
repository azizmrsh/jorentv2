<?php

echo "🔍 اختبار Tenant Panel\n";
echo "==================\n\n";

// Bootstrap Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

try {
    echo "1. فحص نموذج Tenant...\n";
    
    // فحص إذا كان Tenant model موجود ويمكن استخدامه
    $tenantCount = Tenant::count();
    echo "   ✅ عدد المستأجرين: $tenantCount\n";
    
    // فحص أول مستأجر للتأكد من الطرق
    $firstTenant = Tenant::first();
    if ($firstTenant) {
        echo "   ✅ أول مستأجر: {$firstTenant->getFullNameAttribute()}\n";
        echo "   ✅ يمكنه الوصول للـ Panel: " . ($firstTenant->canAccessFilament() ? 'نعم' : 'لا') . "\n";
        echo "   ✅ اسم Filament: {$firstTenant->getFilamentName()}\n";
    }
    
    echo "\n2. فحص TenantPanelProvider...\n";
    
    // فحص إذا كان Provider مسجل
    $providers = app()->getLoadedProviders();
    if (isset($providers['App\\Providers\\Filament\\TenantPanelProvider'])) {
        echo "   ✅ TenantPanelProvider مسجل\n";
    } else {
        echo "   ❌ TenantPanelProvider غير مسجل\n";
    }
    
    echo "\n3. فحص Filament Panels...\n";
    
    // فحص Panels المسجلة
    $filamentManager = app(\Filament\FilamentManager::class);
    $panels = $filamentManager->getPanels();
    
    foreach ($panels as $panelId => $panel) {
        echo "   Panel ID: $panelId\n";
        echo "   Panel Path: " . $panel->getPath() . "\n";
        echo "   Auth Guard: " . ($panel->getAuthGuard() ?? 'default') . "\n";
        echo "   ---\n";
    }
    
    if (isset($panels['tenant'])) {
        echo "   ✅ Tenant Panel موجود\n";
        $tenantPanel = $panels['tenant'];
        echo "   مسار Panel: " . $tenantPanel->getPath() . "\n";
        echo "   Auth Guard: " . $tenantPanel->getAuthGuard() . "\n";
    } else {
        echo "   ❌ Tenant Panel غير موجود\n";
    }
    
    echo "\n4. فحص Guards...\n";
    
    $guards = config('auth.guards');
    if (isset($guards['tenant'])) {
        echo "   ✅ Tenant Guard موجود\n";
        echo "   Driver: " . $guards['tenant']['driver'] . "\n";
        echo "   Provider: " . $guards['tenant']['provider'] . "\n";
    } else {
        echo "   ❌ Tenant Guard غير موجود\n";
    }
    
    echo "\n✅ اختبار Tenant Panel مكتمل!\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "في الملف: " . $e->getFile() . " السطر: " . $e->getLine() . "\n";
}
