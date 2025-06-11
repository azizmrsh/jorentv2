use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

// حذف المستأجر التجريبي إذا كان موجوداً
Tenant::where('email', 'test.tenant@example.com')->delete();

// إنشاء مستأجر جديد
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

echo "✅ تم إنشاء المستأجر: " . $tenant->email;
echo "\n👤 الاسم: " . $tenant->getFilamentName();
echo "\n🎯 يمكن الدخول: " . ($tenant->canAccessFilament() ? 'نعم' : 'لا');

exit;
