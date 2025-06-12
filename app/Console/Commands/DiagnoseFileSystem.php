<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Acc;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use App\Models\Payment;

class DiagnoseFileSystem extends Command
{
    protected $signature = 'diagnose:files {--fix : Attempt to fix missing files} {--create-missing : Create missing directories}';
    protected $description = 'Diagnose file storage system and validate all stored file paths';

    private $issues = [];
    private $stats = [
        'total_files' => 0,
        'existing_files' => 0,
        'missing_files' => 0,
        'invalid_paths' => 0,
        'directories_created' => 0,
        'files_fixed' => 0
    ];

    public function handle()
    {
        $this->info('🔍 Starting File System Diagnosis...');
        $this->info('=====================================');

        // Check storage configuration
        $this->checkStorageConfiguration();

        // Check directory structure
        $this->checkDirectoryStructure();

        // Check model file paths
        $this->checkModelFilePaths();

        // Display results
        $this->displayResults();

        // Fix issues if requested
        if ($this->option('fix') || $this->option('create-missing')) {
            $this->fixIssues();
        }

        return $this->stats['missing_files'] > 0 ? 1 : 0;
    }

    private function checkStorageConfiguration(): void
    {
        $this->info("\n📋 Checking Storage Configuration...");

        // Check APP_URL
        $appUrl = config('app.url');
        $this->line("APP_URL: {$appUrl}");

        // Check storage disk configuration
        $uploadsDisk = config('filesystems.disks.uploads');
        if ($uploadsDisk) {
            $this->line("✅ Custom 'uploads' disk configured");
            $this->line("   Root: " . $uploadsDisk['root']);
            $this->line("   URL: " . $uploadsDisk['url']);
        } else {
            $this->warn("⚠️  No custom 'uploads' disk found");
        }

        // Check storage link
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        if (File::exists($linkPath)) {
            if (is_link($linkPath)) {
                $this->line("✅ Storage link exists and is symbolic");
            } else {
                $this->warn("⚠️  Storage link exists but is not symbolic");
            }
        } else {
            $this->error("❌ Storage link missing - run 'php artisan storage:link'");
            $this->issues[] = 'storage_link_missing';
        }

        // Check uploads directory
        $uploadsPath = public_path('uploads');
        if (File::exists($uploadsPath)) {
            $this->line("✅ Public uploads directory exists");
        } else {
            $this->warn("⚠️  Public uploads directory missing");
            $this->issues[] = 'uploads_directory_missing';
        }
    }

    private function checkDirectoryStructure(): void
    {
        $this->info("\n📁 Checking Directory Structure...");

        $requiredDirectories = [
            'uploads/users',
            'uploads/users/documents',
            'uploads/accs',
            'uploads/accs/documents',
            'uploads/tenants',
            'uploads/tenants/documents',
            'uploads/properties',
            'uploads/units',
            'uploads/contracts',
            'uploads/contracts/signatures',
            'uploads/contracts/pdfs',
            'uploads/contracts/attachments',
            'uploads/payments',
            'uploads/payments/receipts',
            'uploads/payments/proofs',
            'uploads/documents',
            'uploads/documents/legal',
            'uploads/documents/certificates',
            'uploads/templates'
        ];

        foreach ($requiredDirectories as $dir) {
            $fullPath = public_path($dir);
            if (File::exists($fullPath)) {
                $fileCount = count(File::files($fullPath));
                $this->line("✅ {$dir}/ ({$fileCount} files)");
            } else {
                $this->line("❌ {$dir}/ - Missing");
                $this->issues[] = "missing_directory:{$dir}";
            }
        }
    }

    private function checkModelFilePaths(): void
    {
        $this->info("\n🔍 Checking Model File Paths...");

        $models = [
            'users' => [
                'model' => User::class,
                'fields' => ['profile_photo'],
                'base_path' => 'uploads/users'
            ],
            'accs' => [
                'model' => Acc::class,
                'fields' => ['profile_photo', 'document_photo'],
                'base_path' => 'uploads/accs'
            ],
            'tenants' => [
                'model' => Tenant::class,
                'fields' => ['profile_photo', 'document_photo'],
                'base_path' => 'uploads/tenants'
            ],
            'properties' => [
                'model' => Property::class,
                'fields' => ['image_path'],
                'base_path' => 'uploads/properties'
            ],
            'units' => [
                'model' => Unit::class,
                'fields' => ['images'],
                'base_path' => 'uploads/units'
            ],
            'contracts' => [
                'model' => Contract1::class,
                'fields' => ['tenant_signature_path', 'landlord_signature_path', 'witness1_signature_path', 'witness2_signature_path', 'pdf_path'],
                'base_path' => 'uploads/contracts'
            ],
            'payments' => [
                'model' => Payment::class,
                'fields' => ['receipt_attachment', 'payment_proof'],
                'base_path' => 'uploads/payments'
            ]
        ];

        foreach ($models as $modelName => $config) {
            $this->checkModelFiles($modelName, $config);
        }
    }

    private function checkModelFiles(string $modelName, array $config): void
    {
        $this->line("\n📊 Checking {$modelName}...");

        try {
            $model = $config['model'];
            $records = $model::whereNotNull('id')->get();
            
            foreach ($records as $record) {
                foreach ($config['fields'] as $field) {
                    $this->checkFieldFile($record, $field, $config['base_path']);
                }
            }
        } catch (\Exception $e) {
            $this->error("❌ Error checking {$modelName}: " . $e->getMessage());
        }
    }

    private function checkFieldFile($record, string $field, string $basePath): void
    {
        $filePath = $record->{$field};
        
        if (empty($filePath)) {
            return; // Skip null/empty paths
        }

        $this->stats['total_files']++;

        // Handle JSON arrays (for units.images)
        if ($field === 'images' && is_string($filePath)) {
            $images = json_decode($filePath, true);
            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    $this->validateSingleFile($imagePath, $record->getTable(), $record->id, $field);
                }
                return;
            }
        }

        $this->validateSingleFile($filePath, $record->getTable(), $record->id, $field);
    }

    private function validateSingleFile(string $filePath, string $table, int $recordId, string $field): void
    {
        // Clean the path
        $cleanPath = ltrim($filePath, '/');
        
        // Try different possible locations
        $possiblePaths = [
            public_path($cleanPath),
            public_path("uploads/{$cleanPath}"),
            storage_path("app/public/{$cleanPath}"),
            storage_path("app/public/uploads/{$cleanPath}")
        ];

        $found = false;
        foreach ($possiblePaths as $path) {
            if (File::exists($path)) {
                $this->stats['existing_files']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->stats['missing_files']++;
            $issue = [
                'type' => 'missing_file',
                'table' => $table,
                'record_id' => $recordId,
                'field' => $field,
                'path' => $filePath,
                'clean_path' => $cleanPath
            ];
            $this->issues[] = $issue;
            
            $this->line("❌ Missing: {$table}#{$recordId}.{$field} -> {$filePath}");
        }
    }

    private function displayResults(): void
    {
        $this->info("\n📊 Diagnosis Results");
        $this->info("===================");
        
        $this->line("📁 Total files checked: {$this->stats['total_files']}");
        $this->line("✅ Existing files: {$this->stats['existing_files']}");
        $this->line("❌ Missing files: {$this->stats['missing_files']}");
        
        if ($this->stats['missing_files'] > 0) {
            $percentage = round(($this->stats['existing_files'] / $this->stats['total_files']) * 100, 1);
            $this->line("📈 File integrity: {$percentage}%");
        } else {
            $this->line("🎉 All files found! 100% integrity");
        }

        if (!empty($this->issues)) {
            $this->warn("\n⚠️  Issues found:");
            foreach (array_unique($this->issues) as $issue) {
                if (is_string($issue)) {
                    $this->line("  • {$issue}");
                }
            }
        }
    }

    private function fixIssues(): void
    {
        if (empty($this->issues)) {
            $this->info("\n✅ No issues to fix!");
            return;
        }

        $this->info("\n🔧 Attempting to fix issues...");

        foreach ($this->issues as $issue) {
            if (is_string($issue)) {
                $this->fixStringIssue($issue);
            } elseif (is_array($issue) && $issue['type'] === 'missing_file') {
                $this->fixMissingFile($issue);
            }
        }

        $this->info("\n📊 Fix Results:");
        $this->line("📁 Directories created: {$this->stats['directories_created']}");
        $this->line("🔧 Files fixed: {$this->stats['files_fixed']}");
    }

    private function fixStringIssue(string $issue): void
    {
        switch ($issue) {
            case 'storage_link_missing':
                $this->call('storage:link');
                $this->line("✅ Created storage link");
                break;
                
            case 'uploads_directory_missing':
                File::makeDirectory(public_path('uploads'), 0755, true);
                $this->line("✅ Created uploads directory");
                $this->stats['directories_created']++;
                break;
                
            default:
                if (str_starts_with($issue, 'missing_directory:')) {
                    $dir = str_replace('missing_directory:', '', $issue);
                    $fullPath = public_path($dir);
                    File::makeDirectory($fullPath, 0755, true);
                    $this->line("✅ Created directory: {$dir}");
                    $this->stats['directories_created']++;
                }
                break;
        }
    }

    private function fixMissingFile(array $issue): void
    {
        // For now, just log the missing file
        // In a real scenario, you might want to:
        // 1. Create a placeholder image
        // 2. Set the field to null
        // 3. Download from a backup location
        
        $this->line("📝 Logged missing file: {$issue['table']}#{$issue['record_id']}.{$issue['field']}");
    }
}
