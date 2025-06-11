# 🗺️ دليل إعداد Google Places API - دليل شامل

## ✅ **التحديثات المكتملة**

### 1. **تطبيق Google Places Autocomplete في AccResource**
```php
// تم استبدال حقل العنوان العادي بحقل Google Autocomplete
GoogleAutocompleteField::make('address')
    ->label(__('general.Address'))
    ->placeholder(__('general.Start typing to search for an address'))
    ->apiKey(env('GOOGLE_PLACES_API_KEY'))
    ->countries(['JO', 'SA', 'AE', 'EG', 'LB', 'SY', 'IQ', 'KW', 'QA', 'BH', 'OM'])
    ->types(['address'])
    ->helperText(__('general.Type your address and select from Google suggestions'))
    ->columnSpan(2),
```

### 2. **الترجمات المضافة**
```php
// العربية
'Start typing to search for an address' => 'ابدأ بالكتابة للبحث عن عنوان',
'Type your address and select from Google suggestions' => 'اكتب عنوانك واختر من اقتراحات Google',

// الإنجليزية  
'Start typing to search for an address' => 'Start typing to search for an address',
'Type your address and select from Google suggestions' => 'Type your address and select from Google suggestions',
```

### 3. **متغير البيئة المضاف**
```bash
# تمت إضافة المتغير التالي لملف .env
GOOGLE_PLACES_API_KEY=YOUR_GOOGLE_PLACES_API_KEY_HERE
```

---

## 🔑 **كيفية الحصول على Google Places API Key**

### الخطوة 1: إنشاء حساب Google Cloud
1. اذهب إلى [Google Cloud Console](https://console.cloud.google.com/)
2. قم بتسجيل الدخول بحساب Google الخاص بك
3. إذا لم يكن لديك حساب، أنشئ حساباً جديداً

### الخطوة 2: إنشاء مشروع جديد
1. في Google Cloud Console، انقر على "Select a Project" في الأعلى
2. انقر على "New Project"
3. أدخل اسم المشروع (مثل: "Jorent Property Management")
4. انقر على "Create"

### الخطوة 3: تفعيل Places API
1. في القائمة الجانبية، اذهب إلى "APIs & Services" > "Library"
2. ابحث عن "Places API"
3. انقر على "Places API" 
4. انقر على "Enable"

### الخطوة 4: إنشاء API Key
1. اذهب إلى "APIs & Services" > "Credentials"
2. انقر على "Create Credentials" > "API Key"
3. سيتم إنشاء API Key جديد
4. انسخ الـ API Key

### الخطوة 5: تقييد الـ API Key (اختياري ولكن مُوصى به)
1. انقر على الـ API Key الذي تم إنشاؤه
2. في قسم "API restrictions":
   - اختر "Restrict key"
   - اختر "Places API"
3. في قسم "Application restrictions":
   - اختر "HTTP referrers (web sites)"
   - أضف نطاق موقعك (مثل: `*.yourdomain.com/*`)

---

## ⚙️ **التطبيق في المشروع**

### 1. **إضافة API Key إلى ملف .env**
```bash
# استبدل YOUR_GOOGLE_PLACES_API_KEY_HERE بالـ API Key الفعلي
GOOGLE_PLACES_API_KEY=AIzaSyBvOkBdyBVHJiDXxL4YQNjFzAfpf_uDcTI
```

### 2. **إعادة تشغيل التطبيق**
```bash
php artisan config:clear
php artisan cache:clear
php artisan serve
```

---

## 🌍 **المميزات المطبقة**

### ✅ **التركيز على الدول العربية**
- الأردن (JO)
- السعودية (SA) 
- الإمارات (AE)
- مصر (EG)
- لبنان (LB)
- سوريا (SY)
- العراق (IQ)
- الكويت (KW)
- قطر (QA)
- البحرين (BH)
- عُمان (OM)

### ✅ **التركيز على العناوين فقط**
- تم تقييد النتائج لتشمل العناوين فقط (`types: ['address']`)
- يمنع ظهور أماكن أخرى مثل المطاعم أو المحلات

### ✅ **واجهة مستخدم محسنة**
- نص توضيحي باللغة العربية والإنجليزية
- رسائل مساعدة واضحة
- تصميم متناسق مع باقي النموذج

---

## 🔧 **استكشاف الأخطاء**

### مشكلة: الحقل لا يظهر اقتراحات
**الحلول:**
1. تأكد من أن API Key صحيح
2. تأكد من تفعيل Places API في Google Cloud Console
3. تحقق من الشبكة والاتصال بالإنترنت

### مشكلة: رسالة خطأ "API Key invalid"
**الحلول:**
1. تأكد من نسخ API Key بشكل صحيح
2. تأكد من عدم وجود مسافات إضافية
3. تحقق من تقييدات الـ API Key

### مشكلة: الاقتراحات لا تظهر للدول المحددة
**الحلول:**
1. تأكد من أن رموز البلدان صحيحة (مثل JO للأردن)
2. جرب إزالة تقييد البلدان مؤقتاً للاختبار

---

## 💰 **التكلفة والاستخدام**

### الاستخدام المجاني
- Google يوفر 200$ من الاستخدام المجاني شهرياً
- Places Autocomplete: حوالي 2,500 طلب مجاني يومياً
- هذا يكفي لمعظم المشاريع الصغيرة والمتوسطة

### مراقبة الاستخدام
1. اذهب إلى Google Cloud Console
2. انتقل إلى "Billing" > "Reports"
3. راقب استخدام Places API

---

## 🚀 **الخطوات التالية (اختيارية)**

### 1. **إضافة التحقق من صحة العنوان**
```php
// يمكن إضافة validation للتأكد من أن العنوان من Google
->afterStateUpdated(function ($state, $set) {
    if ($state && !str_contains($state, ',')) {
        // رسالة تحذيرية إذا لم يتم اختيار عنوان من Google
    }
})
```

### 2. **حفظ تفاصيل إضافية**
```php
// يمكن حفظ lat/lng والتفاصيل الأخرى
->afterStateUpdated(function ($state, $set, $livewire) {
    // حفظ latitude, longitude, city, country, etc.
})
```

### 3. **إضافة خريطة للعرض**
```php
// يمكن إضافة عرض الموقع على خريطة في صفحة العرض
->infolist([
    MapEntry::make('address')
        ->latitude('latitude')
        ->longitude('longitude')
])
```

---

## ✅ **الملفات المحدثة**

1. **AccResource.php** - تطبيق GoogleAutocompleteField
2. **lang/ar/general.php** - إضافة الترجمات العربية
3. **lang/en/general.php** - إضافة الترجمات الإنجليزية  
4. **.env** - إضافة متغير GOOGLE_PLACES_API_KEY

---

## 📞 **الدعم**

إذا واجهت أي مشاكل:
1. تحقق من [وثائق Google Places API](https://developers.google.com/maps/documentation/places/web-service)
2. راجع [وثائق filament-google-autocomplete-field](https://github.com/tapp-ai/filament-google-autocomplete-field)
3. تحقق من سجلات الأخطاء في Laravel: `storage/logs/laravel.log`

---

**🎉 Google Places API integration is now complete and ready to use!**
