<?php
/**
 * 👤 سكريبت تحميل الصور الشخصية فقط من Unsplash وربطها بقاعدة البيانات
 * 
 * يقوم هذا السكريبت بـ:
 * 1. تحميل صور شخصية للمستخدمين (400x400px)
 * 2. ربطها عشوائياً بالمستخدمين الذين لا يملكون صور
 * 
 * الاستخدام: php download_profile_images_only.php
 */

require_once __DIR__ . '/vendor/autoload.php';

// تحديد مسارات الحفظ للصور الشخصية
$userStorageDir = __DIR__ . '/storage/app/public/uploads/users';
$userPublicDir = __DIR__ . '/public/storage/uploads/users';

$tenantStorageDir = __DIR__ . '/storage/app/public/uploads/tenants';
$tenantPublicDir = __DIR__ . '/public/storage/uploads/tenants';

$accStorageDir = __DIR__ . '/storage/app/public/uploads/accs';
$accPublicDir = __DIR__ . '/public/storage/uploads/accs';

// قائمة جميع المجلدات المطلوبة
$directories = [
    ['storage' => $userStorageDir, 'public' => $userPublicDir, 'name' => 'Users'],
    ['storage' => $tenantStorageDir, 'public' => $tenantPublicDir, 'name' => 'Tenants'],
    ['storage' => $accStorageDir, 'public' => $accPublicDir, 'name' => 'Account Managers'],
];

// إنشاء جميع المجلدات
foreach ($directories as $dir) {
    if (!file_exists($dir['storage'])) {
        mkdir($dir['storage'], 0755, true);
        echo "✅ تم إنشاء مجلد {$dir['name']}: {$dir['storage']}\n";
    }
    
    if (!file_exists($dir['public'])) {
        mkdir($dir['public'], 0755, true);
        echo "✅ تم إنشاء مجلد {$dir['name']} العام: {$dir['public']}\n";
    }
}

// كلمات البحث للصور الشخصية (بدون مسافات لتجنب مشاكل API)
$profileSearchTerms = [
    'portrait',
    'headshot',
    'business',
    'professional',
    'person',
    'face',
    'people',
    'businessman',
    'businesswoman',
    'executive',
    'office',
    'corporate',
    'team',
    'employee',
    'manager',
    'consultant',
    'worker',
    'staff',
    'leader',
    'director'
];

/**
 * تحميل صورة من Unsplash مع آلية إعادة المحاولة
 */
function downloadImageFromUnsplash($searchTerm, $filename, $directory, $width = 400, $height = 400) {
    // قائمة بدائل APIs لـ Unsplash
    $apiEndpoints = [
        "https://source.unsplash.com/{$width}x{$height}/?{$searchTerm}",
        "https://picsum.photos/{$width}/{$height}?random=" . rand(1, 1000),
        "https://source.unsplash.com/featured/{$width}x{$height}/?{$searchTerm}",
    ];
    
    $maxRetries = 3;
    $retryDelay = 2; // ثواني
    
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        foreach ($apiEndpoints as $apiIndex => $url) {
            try {
                echo "   💫 محاولة {$attempt}/{$maxRetries} - API " . ($apiIndex + 1) . "...\n";
                
                // إعداد context للطلب
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'Property Management System/1.0',
                        'method' => 'GET',
                        'header' => "Accept: image/*\r\n"
                    ]
                ]);
                
                // تحميل الصورة
                $imageData = file_get_contents($url, false, $context);
                
                if ($imageData !== false && strlen($imageData) > 1000) { // التأكد من أن الصورة ليست فارغة
                    // حفظ الصورة
                    $fullPath = $directory . '/' . $filename;
                    $result = file_put_contents($fullPath, $imageData);
                    
                    if ($result !== false) {
                        echo "   ✅ تم تحميل: $filename (" . number_format(strlen($imageData) / 1024, 1) . " KB)\n";
                        return true;
                    }
                }
                
            } catch (Exception $e) {
                echo "   ⚠️  خطأ API " . ($apiIndex + 1) . ": " . $e->getMessage() . "\n";
            }
        }
        
        if ($attempt < $maxRetries) {
            echo "   ⏳ انتظار {$retryDelay} ثانية قبل المحاولة التالية...\n";
            sleep($retryDelay);
            $retryDelay += 1; // زيادة وقت الانتظار تدريجياً
        }
    }
    
    echo "   ❌ فشل في تحميل $filename بعد {$maxRetries} محاولات\n";
    return false;
}

/**
 * تحميل صور شخصية متنوعة
 */
function downloadProfileImages($count = 20, $type = 'users') {
    global $userStorageDir, $userPublicDir, $tenantStorageDir, $tenantPublicDir, 
           $accStorageDir, $accPublicDir, $profileSearchTerms;
    
    // تحديد المجلدات حسب النوع
    switch($type) {
        case 'tenants':
            $storageDir = $tenantStorageDir;
            $publicDir = $tenantPublicDir;
            $prefix = 'tenant_profile';
            break;
        case 'accs':
            $storageDir = $accStorageDir;
            $publicDir = $accPublicDir;
            $prefix = 'acc_profile';
            break;
        default:
            $storageDir = $userStorageDir;
            $publicDir = $userPublicDir;
            $prefix = 'user_profile';
    }
    
    $downloadedImages = [];
    $successCount = 0;
    
    echo "👤 بدء تحميل $count صورة شخصية للـ $type...\n\n";
    
    for ($i = 1; $i <= $count; $i++) {
        $searchTerm = $profileSearchTerms[array_rand($profileSearchTerms)];
        $filename = $prefix . "_" . $i . "_" . time() . "_" . rand(1000, 9999) . ".jpg";
          echo "[$i/$count] تحميل صورة شخصية: $searchTerm...\n";
        
        // تحميل إلى مجلد storage
        if (downloadImageFromUnsplash($searchTerm, $filename, $storageDir, 400, 400)) {
            // نسخ إلى مجلد public أيضاً
            copy($storageDir . '/' . $filename, $publicDir . '/' . $filename);
            
            $downloadedImages[] = $filename;
            $successCount++;
        }
        
        // توقف قصير لتجنب الحمل الزائد على الخادم
        echo "   ⏳ انتظار قصير...\n";
        sleep(rand(2, 4));
    }
    
    echo "\n✅ تم تحميل $successCount صورة شخصية للـ $type بنجاح!\n";
    return $downloadedImages;
}

/**
 * ربط الصور الشخصية بالمستخدمين
 */
function assignImagesToUsers($images, $type = 'users') {
    // إعداد قاعدة البيانات
    try {
        $config = require __DIR__ . '/config/database.php';
        $dbConfig = $config['connections']['mysql'];
        
        $pdo = new PDO(
            "mysql:host={$dbConfig['host']};dbname={$dbConfig['database']};charset=utf8mb4",
            $dbConfig['username'],
            $dbConfig['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "✅ تم الاتصال بقاعدة البيانات للمستخدمين\n";
        
    } catch (PDOException $e) {
        echo "❌ فشل الاتصال بقاعدة البيانات: " . $e->getMessage() . "\n";
        return false;
    }
    
    try {
        // تحديد جدول قاعدة البيانات والحقول حسب النوع
        switch($type) {
            case 'tenants':
                $table = 'tenants';
                $nameField = 'firstname';
                $pathPrefix = 'uploads/tenants/';
                break;
            case 'accs':
                $table = 'accs';
                $nameField = 'firstname';
                $pathPrefix = 'uploads/accs/';
                break;
            default:
                $table = 'users';
                $nameField = 'name';
                $pathPrefix = 'uploads/users/';
        }
        
        // جلب المستخدمين الذين لا يملكون صور شخصية
        $stmt = $pdo->query("SELECT id, $nameField as name FROM $table WHERE profile_photo IS NULL OR profile_photo = '' ORDER BY RAND()");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($users)) {
            echo "⚠️  جميع مستخدمي $type لديهم صور شخصية بالفعل!\n";
            return true;
        }
        
        echo "📊 تم العثور على " . count($users) . " مستخدم من $type بدون صور شخصية\n";
        
        // توزيع الصور عشوائياً على المستخدمين
        $assignedCount = 0;
        shuffle($images); // خلط الصور
        
        foreach ($images as $index => $image) {
            if ($index < count($users)) {
                $user = $users[$index];
                $imagePath = $pathPrefix . $image;
                
                // تحديث المستخدم بمسار الصورة
                $updateStmt = $pdo->prepare("UPDATE $table SET profile_photo = ? WHERE id = ?");
                $result = $updateStmt->execute([$imagePath, $user['id']]);
                
                if ($result) {
                    echo "🔗 تم ربط صورة {$image} بـ {$user['name']} ($type)\n";
                    $assignedCount++;
                }
            }
        }
        
        echo "\n✅ تم ربط $assignedCount صورة شخصية بـ $type!\n";
        return true;
        
    } catch (PDOException $e) {
        echo "❌ خطأ في قاعدة البيانات: " . $e->getMessage() . "\n";
        return false;
    }
}

/**
 * تشغيل السكريبت الرئيسي
 */
function main() {
    echo "👤 مرحباً بك في سكريبت تحميل الصور الشخصية!\n";
    echo "==============================================\n\n";
    
    $userImages = [];
    $tenantImages = [];
    $accImages = [];
    
    // تحميل صور المستخدمين
    echo "📋 تحميل صور المستخدمين العاديين...\n";
    echo str_repeat("-", 50) . "\n";
    $userImages = downloadProfileImages(20, 'users');
    if (!empty($userImages)) {
        echo "\n🔗 ربط صور المستخدمين بقاعدة البيانات...\n";
        assignImagesToUsers($userImages, 'users');
    }
    
    // تحميل صور المستأجرين
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📋 تحميل صور المستأجرين...\n";
    echo str_repeat("-", 50) . "\n";
    $tenantImages = downloadProfileImages(20, 'tenants');
    if (!empty($tenantImages)) {
        echo "\n🔗 ربط صور المستأجرين بقاعدة البيانات...\n";
        assignImagesToUsers($tenantImages, 'tenants');
    }
    
    // تحميل صور مديري الحسابات
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📋 تحميل صور مديري الحسابات...\n";
    echo str_repeat("-", 50) . "\n";
    $accImages = downloadProfileImages(10, 'accs');
    if (!empty($accImages)) {
        echo "\n🔗 ربط صور مديري الحسابات بقاعدة البيانات...\n";
        assignImagesToUsers($accImages, 'accs');
    }
    
    // عرض النتائج النهائية
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎉 تم إكمال تحميل الصور الشخصية بنجاح!\n";
    echo str_repeat("=", 60) . "\n";
    
    if (!empty($userImages)) {
        echo "👤 صور المستخدمين: " . count($userImages) . " صورة\n";
        echo "   📁 المسار: storage/app/public/uploads/users/\n";
        echo "   🌐 الرابط: /storage/uploads/users/\n\n";
    }
    
    if (!empty($tenantImages)) {
        echo "🏠 صور المستأجرين: " . count($tenantImages) . " صورة\n";
        echo "   📁 المسار: storage/app/public/uploads/tenants/\n";
        echo "   🌐 الرابط: /storage/uploads/tenants/\n\n";
    }
    
    if (!empty($accImages)) {
        echo "💼 صور مديري الحسابات: " . count($accImages) . " صورة\n";
        echo "   📁 المسار: storage/app/public/uploads/accs/\n";
        echo "   🌐 الرابط: /storage/uploads/accs/\n\n";
    }
    
    echo "✨ يمكنك الآن الدخول إلى لوحة التحكم لرؤية الصور الشخصية!\n";
}

// تشغيل السكريبت
main();

?>
