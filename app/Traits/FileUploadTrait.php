<?php

namespace App\Traits;

use Filament\Forms\Components\FileUpload;

trait FileUploadTrait
{    /**
     * إنشاء حقل رفع صورة شخصية محسن للسرعة
     */
    public static function profilePhotoUpload(): FileUpload
    {
        return FileUpload::make('profile_photo')
            ->label(__('general.Profile Photo'))
            ->image()
            ->directory('users')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(1024) // 1MB للسرعة القصوى
            ->acceptedFileTypes(['image/jpeg', 'image/webp']) // أسرع الصيغ
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('1:1')
            ->imageResizeTargetWidth('120') // تصغير أكثر للسرعة
            ->imageResizeTargetHeight('120')
            ->imageEditor(false) // تعطيل المحرر لسرعة أكبر
            ->previewable(false) // تعطيل المعاينة للأداء
            ->openable(false) // تعطيل الفتح لتقليل العمليات
            ->downloadable(false) // تعطيل التحميل لتوفير الموارد
            ->moveFiles()
            ->loadingIndicatorPosition('center')
            ->uploadingMessage(__('general.Fast uploading...'))
            ->helperText(__('general.Upload profile photo (max 1MB, auto-optimized for speed)'))
            ->hintIcon('heroicon-o-bolt')
            ->hint(__('general.Optimized for fast loading - WebP/JPEG only'));
    }

    /**
     * إنشاء حقل رفع صورة وثيقة محسن للسرعة
     */
    public static function documentPhotoUpload(): FileUpload
    {
        return FileUpload::make('document_photo')
            ->label(__('general.Document Photo'))
            ->image()
            ->directory('users/documents')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(1536) // تقليل أكثر إلى 1.5MB للسرعة
            ->acceptedFileTypes(['image/jpeg', 'image/webp']) // أسرع الصيغ
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('300') // تصغير أكثر للسرعة
            ->imageResizeTargetHeight('200')
            ->previewable(false) // تعطيل للسرعة
            ->openable(false) // تعطيل للسرعة
            ->downloadable(false) // تعطيل للسرعة
            ->moveFiles()
            ->loadingIndicatorPosition('center')
            ->uploadingMessage(__('general.Fast processing...'))
            ->helperText(__('general.Upload document photo (max 1.5MB, optimized for speed)'))
            ->hintIcon('heroicon-o-bolt')
            ->hint(__('general.Fast loading - WebP/JPEG only'));
    }    /**
     * إنشاء حقل رفع صورة عقار
     */
    public static function propertyImageUpload(): FileUpload
    {
        return FileUpload::make('image_path')
            ->label('Property Image')
            ->image()
            ->directory('properties')
            ->disk('uploads')
            ->visibility('public')
            ->imageEditor()
            ->imageEditorAspectRatios([
                '16:9',
                '4:3',
                '1:1',
            ])
            ->maxSize(10240) // 10MB للعقارات
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->helperText('Upload a high-quality property image (max 10MB)')
            ->columnSpanFull();
    }

    /**
     * إنشاء حقل رفع صور متعددة للوحدات
     */
    public static function unitImagesUpload(): FileUpload
    {
        return FileUpload::make('images')
            ->label('Unit Images')
            ->image()
            ->multiple()
            ->directory('units')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(5120) // 5MB لكل صورة
            ->maxFiles(10) // حد أقصى 10 صور
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imageResizeMode('cover')
            ->imageResizeTargetWidth('800')
            ->imageResizeTargetHeight('600')
            ->helperText('Upload up to 10 unit images (max 5MB each, 800x600px recommended)')
            ->columnSpanFull();
    }

    /**
     * إنشاء حقل رفع توقيع
     */
    public static function signatureUpload(string $fieldName, string $label): FileUpload
    {
        return FileUpload::make($fieldName)
            ->label($label)
            ->image()
            ->directory('contracts/signatures')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(2048) // 2MB للتواقيع
            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('400')
            ->imageResizeTargetHeight('200')
            ->helperText('Upload signature image (max 2MB, PNG/JPEG/SVG, 400x200px recommended)');
    }

    /**
     * إنشاء حقل رفع ملف PDF
     */    public static function pdfUpload(string $fieldName, string $label, string $directory = 'documents'): FileUpload
    {
        return FileUpload::make($fieldName)
            ->label($label)
            ->directory($directory)
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(20480) // 20MB للملفات            ->acceptedFileTypes(['application/pdf'])
            ->helperText('Upload PDF file (max 20MB)');
    }

    /**
     * إنشاء حقل رفع مرفق دفعة
     */
    public static function paymentAttachmentUpload(): FileUpload
    {
        return FileUpload::make('attachment')
            ->label('Payment Attachment')
            ->directory('payments/receipts')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(10240) // 10MB
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->helperText('Upload payment receipt or proof (max 10MB, JPEG/PNG/PDF)');    }

    /**
     * إنشاء حقل رفع ملف عام
     */
    public static function generalFileUpload(
        string $fieldName, 
        string $label, 
        string $directory = 'documents',
        array $acceptedTypes = ['application/pdf', 'image/jpeg', 'image/png'],
        int $maxSize = 10240,
        string $helperText = null
    ): FileUpload {
        return FileUpload::make($fieldName)
            ->label($label)
            ->directory($directory)
            ->disk('uploads')
            ->visibility('public')
            ->maxSize($maxSize)
            ->acceptedFileTypes($acceptedTypes)
            ->helperText($helperText ?? "Upload file (max {$maxSize}KB)");
    }    /**
     * الحصول على تكوين أساسي لرفع الملفات
     */
    public static function baseFileUploadConfig(): array
    {
        return [
            'disk' => 'uploads',
            'visibility' => 'public',
            'maxSize' => 5120, // 5MB افتراضي
        ];
    }

    /**
     * الحصول على أنواع ملفات الصور المسموحة
     */
    public static function getImageFileTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    }

    /**
     * الحصول على أنواع ملفات الوثائق المسموحة
     */
    public static function getDocumentFileTypes(): array
    {
        return ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    }

    /**
     * الحصول على تكوين محسن للصور
     */
    public static function getOptimizedImageConfig(): array
    {
        return [
            'imageResizeMode' => 'cover',
            'imageResizeTargetWidth' => '800',
            'imageResizeTargetHeight' => '600',
            'imageEditor' => true,
            'imageEditorAspectRatios' => ['16:9', '4:3', '1:1'],
        ];
    }
}