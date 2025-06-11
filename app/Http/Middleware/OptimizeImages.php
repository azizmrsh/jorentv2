<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class OptimizeImages
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // تحسين الصور عند الرفع
        if ($request->hasFile('profile_photo') || $request->hasFile('document_photo')) {
            $this->optimizeUploadedImages($request);
        }

        return $response;
    }

    /**
     * تحسين الصور المرفوعة للسرعة
     */
    private function optimizeUploadedImages(Request $request)
    {
        // تحسين الصورة الشخصية
        if ($request->hasFile('profile_photo')) {
            $this->optimizeProfilePhoto($request->file('profile_photo'));
        }

        // تحسين صورة الوثيقة
        if ($request->hasFile('document_photo')) {
            $this->optimizeDocumentPhoto($request->file('document_photo'));
        }
    }

    /**
     * تحسين الصورة الشخصية
     */
    private function optimizeProfilePhoto($file)
    {
        if (!$file || !$file->isValid()) {
            return;
        }

        try {
            // إنشاء صورة محسنة
            $image = Image::make($file);
            
            // تحسين الحجم والجودة للسرعة
            $image->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ضغط الصورة للسرعة (جودة 75%)
            $image->encode('jpg', 75);

            // حفظ الصورة المحسنة
            $optimizedPath = 'users/optimized_' . time() . '.jpg';
            Storage::disk('uploads')->put($optimizedPath, $image->stream());

            // استبدال المسار بالصورة المحسنة
            $file->storeAs('users', basename($optimizedPath), 'uploads');

        } catch (\Exception $e) {
            // في حالة الخطأ، استخدم الصورة الأصلية
            \Log::warning('Failed to optimize profile photo: ' . $e->getMessage());
        }
    }

    /**
     * تحسين صورة الوثيقة
     */
    private function optimizeDocumentPhoto($file)
    {
        if (!$file || !$file->isValid()) {
            return;
        }

        try {
            // إنشاء صورة محسنة
            $image = Image::make($file);
            
            // تحسين الحجم للسرعة
            $image->resize(300, 200, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // ضغط الصورة (جودة 70%)
            $image->encode('jpg', 70);

            // حفظ الصورة المحسنة
            $optimizedPath = 'users/documents/optimized_' . time() . '.jpg';
            Storage::disk('uploads')->put($optimizedPath, $image->stream());

            // استبدال المسار
            $file->storeAs('users/documents', basename($optimizedPath), 'uploads');

        } catch (\Exception $e) {
            // في حالة الخطأ، استخدم الصورة الأصلية
            \Log::warning('Failed to optimize document photo: ' . $e->getMessage());
        }
    }

    /**
     * تحويل الصورة إلى WebP للسرعة القصوى
     */
    private function convertToWebP($image, $quality = 80)
    {
        try {
            // تحقق من دعم WebP
            if (function_exists('imagewebp')) {
                return $image->encode('webp', $quality);
            }
        } catch (\Exception $e) {
            \Log::info('WebP conversion failed, using JPEG: ' . $e->getMessage());
        }

        // العودة إلى JPEG إذا فشل WebP
        return $image->encode('jpg', $quality);
    }

    /**
     * تحسين الصور الموجودة (للاستخدام اليدوي)
     */
    public static function optimizeExistingImages()
    {
        try {
            $images = Storage::disk('uploads')->allFiles('users');
            
            foreach ($images as $imagePath) {
                if (self::isImageFile($imagePath)) {
                    self::optimizeExistingImage($imagePath);
                }
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to optimize existing images: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * التحقق من نوع الملف
     */
    private static function isImageFile($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp']);
    }

    /**
     * تحسين صورة موجودة
     */
    private static function optimizeExistingImage($imagePath)
    {
        try {
            if (Storage::disk('uploads')->exists($imagePath)) {
                $image = Image::make(Storage::disk('uploads')->get($imagePath));
                
                // تصغير إذا كانت كبيرة
                if ($image->width() > 300 || $image->height() > 300) {
                    $image->resize(300, 300, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // ضغط
                $compressed = $image->encode('jpg', 75);
                
                // حفظ الصورة المحسنة
                Storage::disk('uploads')->put($imagePath, $compressed);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to optimize image ' . $imagePath . ': ' . $e->getMessage());
        }
    }
}
