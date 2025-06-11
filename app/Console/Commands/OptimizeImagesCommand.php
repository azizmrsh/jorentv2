<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class OptimizeImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize 
                          {--force : Force optimization of all images}
                          {--profile-only : Optimize only profile photos}
                          {--document-only : Optimize only document photos}
                          {--max-size=300 : Maximum image dimension}
                          {--quality=75 : Image quality (1-100)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize images for better performance and faster loading';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting image optimization for better performance...');
        
        $force = $this->option('force');
        $profileOnly = $this->option('profile-only');
        $documentOnly = $this->option('document-only');
        $maxSize = (int) $this->option('max-size');
        $quality = (int) $this->option('quality');

        $optimized = 0;
        $skipped = 0;
        $errors = 0;

        // تحسين الصور الشخصية
        if (!$documentOnly) {
            $this->info('📸 Optimizing profile photos...');
            $result = $this->optimizeProfilePhotos($maxSize, $quality, $force);
            $optimized += $result['optimized'];
            $skipped += $result['skipped'];
            $errors += $result['errors'];
        }

        // تحسين صور الوثائق
        if (!$profileOnly) {
            $this->info('📄 Optimizing document photos...');
            $result = $this->optimizeDocumentPhotos($maxSize, $quality, $force);
            $optimized += $result['optimized'];
            $skipped += $result['skipped'];
            $errors += $result['errors'];
        }

        // تنظيف الصور المكررة
        $this->info('🧹 Cleaning duplicate images...');
        $cleaned = $this->cleanDuplicateImages();

        // إحصائيات النتائج
        $this->newLine();
        $this->info("✅ Optimization completed!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Images Optimized', $optimized],
                ['Images Skipped', $skipped],
                ['Errors', $errors],
                ['Duplicates Cleaned', $cleaned],
            ]
        );

        // حساب توفير المساحة
        $this->calculateSpaceSaving();

        return 0;
    }

    /**
     * تحسين الصور الشخصية
     */
    private function optimizeProfilePhotos($maxSize, $quality, $force)
    {
        $optimized = 0;
        $skipped = 0;
        $errors = 0;

        $images = Storage::disk('uploads')->allFiles('users');
        $profileImages = array_filter($images, function($path) {
            return !str_contains($path, 'documents/') && $this->isImageFile($path);
        });

        $progressBar = $this->output->createProgressBar(count($profileImages));
        $progressBar->start();

        foreach ($profileImages as $imagePath) {
            try {
                if ($this->optimizeImage($imagePath, 100, 100, $quality, $force)) {
                    $optimized++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error optimizing {$imagePath}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return ['optimized' => $optimized, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * تحسين صور الوثائق
     */
    private function optimizeDocumentPhotos($maxSize, $quality, $force)
    {
        $optimized = 0;
        $skipped = 0;
        $errors = 0;

        $images = Storage::disk('uploads')->allFiles('users/documents');
        $documentImages = array_filter($images, function($path) {
            return $this->isImageFile($path);
        });

        $progressBar = $this->output->createProgressBar(count($documentImages));
        $progressBar->start();

        foreach ($documentImages as $imagePath) {
            try {
                if ($this->optimizeImage($imagePath, 300, 200, $quality, $force)) {
                    $optimized++;
                } else {
                    $skipped++;
                }
            } catch (\Exception $e) {
                $errors++;
                $this->error("Error optimizing {$imagePath}: " . $e->getMessage());
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        return ['optimized' => $optimized, 'skipped' => $skipped, 'errors' => $errors];
    }

    /**
     * تحسين صورة واحدة
     */
    private function optimizeImage($imagePath, $maxWidth, $maxHeight, $quality, $force)
    {
        if (!Storage::disk('uploads')->exists($imagePath)) {
            return false;
        }

        $originalSize = Storage::disk('uploads')->size($imagePath);
        
        // تخطي الصور الصغيرة إلا إذا كان فرض التحسين
        if (!$force && $originalSize < 50000) { // أقل من 50KB
            return false;
        }

        try {
            $imageContent = Storage::disk('uploads')->get($imagePath);
            $image = Image::make($imageContent);

            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // تحسين الحجم إذا كان ضرورياً
            if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $image->resize($maxWidth, $maxHeight, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // ضغط الصورة
            $optimized = $image->encode('jpg', $quality);

            // حفظ الصورة المحسنة إذا كانت أصغر
            $newSize = strlen($optimized);
            if ($newSize < $originalSize || $force) {
                Storage::disk('uploads')->put($imagePath, $optimized);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * تنظيف الصور المكررة
     */
    private function cleanDuplicateImages()
    {
        $cleaned = 0;
        $hashes = [];

        $allImages = Storage::disk('uploads')->allFiles('users');
        
        foreach ($allImages as $imagePath) {
            if (!$this->isImageFile($imagePath)) {
                continue;
            }

            try {
                $content = Storage::disk('uploads')->get($imagePath);
                $hash = md5($content);

                if (isset($hashes[$hash])) {
                    // صورة مكررة - احذف الأحدث
                    Storage::disk('uploads')->delete($imagePath);
                    $cleaned++;
                    $this->line("🗑️  Deleted duplicate: {$imagePath}");
                } else {
                    $hashes[$hash] = $imagePath;
                }
            } catch (\Exception $e) {
                $this->warn("Could not process {$imagePath}: " . $e->getMessage());
            }
        }

        return $cleaned;
    }

    /**
     * حساب توفير المساحة
     */
    private function calculateSpaceSaving()
    {
        try {
            $totalSize = 0;
            $imageCount = 0;

            $allImages = Storage::disk('uploads')->allFiles('users');
            
            foreach ($allImages as $imagePath) {
                if ($this->isImageFile($imagePath)) {
                    $totalSize += Storage::disk('uploads')->size($imagePath);
                    $imageCount++;
                }
            }

            $averageSize = $imageCount > 0 ? $totalSize / $imageCount : 0;
            $totalSizeMB = round($totalSize / 1024 / 1024, 2);
            $averageSizeKB = round($averageSize / 1024, 2);

            $this->newLine();
            $this->info("📊 Storage Statistics:");
            $this->line("Total Images: {$imageCount}");
            $this->line("Total Size: {$totalSizeMB} MB");
            $this->line("Average Size: {$averageSizeKB} KB");

        } catch (\Exception $e) {
            $this->warn("Could not calculate storage statistics: " . $e->getMessage());
        }
    }

    /**
     * التحقق من نوع الملف
     */
    private function isImageFile($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }
}
