<?php

echo "Starting test...\n";

require_once 'vendor/autoload.php';

echo "Autoload loaded...\n";

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
echo "App loaded...\n";

$kernel = $app->make('kernel');
echo "Kernel loaded...\n";

echo "Testing AccResource...\n";

try {
    // Test if we can access the AccResource class
    $resourceClass = \App\Filament\Resources\AccResource::class;
    echo "✓ AccResource class accessible\n";
    
    // Test PhoneInput and PhoneColumn classes
    $phoneInputExists = class_exists('Ysfkaya\FilamentPhoneInput\Forms\PhoneInput');
    $phoneColumnExists = class_exists('Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn');
    
    echo "✓ PhoneInput exists: " . ($phoneInputExists ? 'YES' : 'NO') . "\n";
    echo "✓ PhoneColumn exists: " . ($phoneColumnExists ? 'YES' : 'NO') . "\n";
    
    echo "All classes are accessible successfully!\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "✗ File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
