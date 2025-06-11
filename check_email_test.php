<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Email Configuration Test ===\n";

// Check mail configuration
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_FROM_ADDRESS: " . config('mail.from.address') . "\n";
echo "MAIL_FROM_NAME: " . config('mail.from.name') . "\n";

// Test SMTP connection
echo "\n=== Testing SMTP Connection ===\n";
try {
    $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
        config('mail.mailers.smtp.host'),
        config('mail.mailers.smtp.port'),
        config('mail.mailers.smtp.encryption') === 'tls'
    );
    
    $transport->setUsername(config('mail.mailers.smtp.username'));
    $transport->setPassword(config('mail.mailers.smtp.password'));
    
    echo "SMTP connection test: ";
    $transport->start();
    echo "SUCCESS ✓\n";
    
} catch (Exception $e) {
    echo "SMTP connection test: FAILED ✗\n";
    echo "Error: " . $e->getMessage() . "\n";
}

// Test sending a simple email
echo "\n=== Testing Email Send ===\n";
try {
    Mail::raw('This is a test email from JoRent system.', function ($message) {
        $message->to('jorent2025@gmail.com')
                ->subject('JoRent Test Email - ' . date('Y-m-d H:i:s'));
    });
    echo "Test email sent: SUCCESS ✓\n";
} catch (Exception $e) {
    echo "Test email sent: FAILED ✗\n";
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Check Queue Configuration ===\n";
echo "QUEUE_CONNECTION: " . config('queue.default') . "\n";

// Check if there are failed queue jobs
try {
    $failedJobs = \Illuminate\Support\Facades\DB::table('failed_jobs')->count();
    echo "Failed queue jobs: " . $failedJobs . "\n";
} catch (Exception $e) {
    echo "Could not check failed jobs: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
