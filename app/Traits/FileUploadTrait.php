<?php

namespace App\Traits; // force touch

use Filament\Forms\Components\FileUpload;

trait FileUploadTrait
{
    /**
     * Create profile photo upload field
     * Saves to public/uploads/users/
     */
    public static function profilePhotoUpload(): FileUpload
    {
        return FileUpload::make('profile_photo')
            ->label(__('general.Profile Photo'))
            ->image()
            ->directory('users')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imageResizeMode('cover')
            ->imageCropAspectRatio('1:1')
            ->imageResizeTargetWidth('300')
            ->imageResizeTargetHeight('300')
            ->previewable()
            ->openable()
            ->downloadable()
            ->moveFiles()
            ->helperText(__('general.Upload profile photo (max 2MB, 300x300px)'))
            ->hintIcon('heroicon-o-user-circle');
    }

    /**
     * Create document photo upload field
     * Saves to public/uploads/{model}/documents/
     */
    public static function documentPhotoUpload(): FileUpload
    {
        return FileUpload::make('document_photo')
            ->label(__('general.Document Photo'))
            ->directory('users/documents')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(5120)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('800')
            ->imageResizeTargetHeight('600')
            ->previewable()
            ->openable()
            ->downloadable()
            ->moveFiles()
            ->helperText(__('general.Upload document photo or PDF (max 5MB)'))
            ->hintIcon('heroicon-o-document');
    }

    /**
     * Create property image upload field
     * Saves to public/uploads/properties/
     */
    public static function propertyImageUpload(): FileUpload
    {
        return FileUpload::make('image_path')
            ->label(__('general.Property Image'))
            ->image()
            ->directory('properties')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(10240)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imageEditor()
            ->imageEditorAspectRatios(['16:9', '4:3', '1:1'])
            ->imageResizeMode('cover')
            ->imageResizeTargetWidth('1200')
            ->imageResizeTargetHeight('800')
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload property image (max 10MB, high quality)'))
            ->columnSpanFull();
    }

    /**
     * Create unit images upload field (multiple)
     * Saves to public/uploads/units/
     */
    public static function unitImagesUpload(): FileUpload
    {
        return FileUpload::make('images')
            ->label(__('general.Unit Images'))
            ->image()
            ->multiple()
            ->directory('units')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(5120)
            ->maxFiles(10)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->imageResizeMode('cover')
            ->imageResizeTargetWidth('800')
            ->imageResizeTargetHeight('600')
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload up to 10 unit images (max 5MB each)'))
            ->columnSpanFull();
    }

    /**
     * Create signature upload field
     * Saves to public/uploads/contracts/signatures/
     */
    public static function signatureUpload(string $fieldName, string $label): FileUpload
    {
        return FileUpload::make($fieldName)
            ->label($label)
            ->image()
            ->directory('contracts/signatures')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(2048)
            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml'])
            ->imageResizeMode('contain')
            ->imageResizeTargetWidth('400')
            ->imageResizeTargetHeight('200')
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload signature (max 2MB, 400x200px)'));
    }

    /**
     * Create PDF upload field
     * Saves to public/uploads/contracts/pdfs/
     */
    public static function pdfUpload(string $fieldName, string $label, string $directory = 'documents'): FileUpload
    {
        return FileUpload::make($fieldName)
            ->label($label)
            ->directory($directory)
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(20480)
            ->acceptedFileTypes(['application/pdf'])
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload PDF file (max 20MB)'));
    }

    /**
     * Create payment attachment upload field
     * Saves to public/uploads/payments/receipts/
     */
    public static function paymentAttachmentUpload(): FileUpload
    {
        return FileUpload::make('receipt_attachment')
            ->label(__('general.Payment Receipt'))
            ->directory('payments/receipts')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(10240)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload payment receipt (max 10MB, JPEG/PNG/PDF)'));
    }

    /**
     * Create payment proof upload field
     * Saves to public/uploads/payments/proofs/
     */
    public static function paymentProofUpload(): FileUpload
    {
        return FileUpload::make('payment_proof')
            ->label(__('general.Payment Proof'))
            ->directory('payments/proofs')
            ->disk('uploads')
            ->visibility('public')
            ->maxSize(10240)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText(__('general.Upload payment proof (max 10MB, JPEG/PNG/PDF)'));
    }

    /**
     * Create general file upload field
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
            ->previewable()
            ->openable()
            ->downloadable()
            ->helperText($helperText ?? "Upload file (max {$maxSize}KB)");
    }

    /**
     * Get base file upload configuration
     */
    public static function baseFileUploadConfig(): array
    {
        return [
            'disk' => 'uploads',
            'visibility' => 'public',
            'maxSize' => 5120,
        ];
    }

    /**
     * Get allowed image file types
     */
    public static function getImageFileTypes(): array
    {
        return ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    }

    /**
     * Get allowed PDF file types
     */
    public static function getPdfFileTypes(): array
    {
        return ['application/pdf'];
    }

    /**
     * Get allowed document file types
     */
    public static function getDocumentFileTypes(): array
    {
        return [
            'application/pdf', 
            'application/msword', 
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
    }
}