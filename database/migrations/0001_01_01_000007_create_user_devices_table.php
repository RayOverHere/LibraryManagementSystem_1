<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: user_devices
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * FK: user_id → users.id (ON DELETE CASCADE)
     * Unique: token
     */
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();                                                      // PK: BIGINT UNSIGNED
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  // FK → users.id
            $table->string('device_name');                                     // VARCHAR(255)
            $table->string('token', 64)->unique();                             // VARCHAR(64), UNIQUE
            $table->string('ip_address', 45)->nullable();                      // VARCHAR(45), NULLABLE (IPv6)
            $table->timestamp('last_active_at')->nullable();                   // TIMESTAMP, NULLABLE
            $table->timestamps();                                              // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};
