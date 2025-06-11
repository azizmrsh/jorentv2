<?php

echo "Starting PhoneColumn test...\n";

require_once 'vendor/autoload.php';

echo "Autoload included successfully\n";

// Test if PhoneColumn class exists and can be instantiated
try {
    $className = 'Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn';
    
    if (class_exists($className)) {
        echo "✓ PhoneColumn class exists\n";
        
        // Try to get class methods
        $reflection = new ReflectionClass($className);
        echo "✓ Class can be reflected\n";
        echo "Available methods: " . implode(', ', array_slice(get_class_methods($className), 0, 5)) . "...\n";
        
    } else {
        echo "✗ PhoneColumn class NOT found\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Also test PhoneInput class
try {
    $phoneInputClass = 'Ysfkaya\FilamentPhoneInput\Forms\PhoneInput';
    
    if (class_exists($phoneInputClass)) {
        echo "✓ PhoneInput class exists\n";
    } else {
        echo "✗ PhoneInput class NOT found\n";
    }
    
} catch (Exception $e) {
    echo "✗ PhoneInput Error: " . $e->getMessage() . "\n";
}
