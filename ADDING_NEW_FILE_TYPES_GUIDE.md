# 📋 دليل إضافة أنواع ملفات جديدة للنظام

## 📖 نظرة عامة
هذا الدليل يوضح خطوة بخطوة كيفية إضافة أنواع ملفات جديدة لنظام Jorent مع شرح تفصيلي لكل خطوة باللغة العربية.

---

## 🏗️ هيكل النظام الحالي

### 📁 مجلدات التخزين المنظمة
```
public/uploads/
├── contracts/
│   ├── signatures/         # تواقيع العقود
│   └── *.pdf              # ملفات العقود PDF
├── properties/            # صور العقارات
├── users/
│   ├── documents/         # وثائق المستخدمين
│   └── *.jpg,*.png       # صور المستخدمين الشخصية
├── payments/
│   └── receipts/         # إيصالات الدفع
└── units/                # صور الوحدات السكنية
```

### 🔧 الأدوات المستخدمة
- **FileUploadTrait**: يحتوي على وظائف رفع الملفات المشتركة
- **Filament Resources**: للتحكم في واجهة الإدارة
- **Laravel Storage**: نظام تخزين الملفات
- **Filesystem Disk 'uploads'**: القرص المخصص للرفع

---

## ⚡ إضافة نوع ملف جديد - خطوة بخطوة

### 🎯 مثال: إضافة نوع ملف "Legal Documents" (الوثائق القانونية)

#### الخطوة 1: إنشاء وظيفة في FileUploadTrait

<function_calls>
<invoke name="read_file">
<parameter name="filePath">c:\Users\mzyz2\Desktop\الجامعة\last\FinalProject\project\jorentV2\app\Traits\FileUploadTrait.php
