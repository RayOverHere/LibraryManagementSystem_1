<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

\App\Models\User::updateOrCreate(
    ['email' => 'admin@noble.com'],
    [
        'name' => 'Admin',
        'phone' => '1234567890',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'admin'
    ]
);

echo "Admin account created successfully.\n";
