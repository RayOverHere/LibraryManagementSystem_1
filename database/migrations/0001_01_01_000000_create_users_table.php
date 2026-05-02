<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: users
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * Unique: email, phone
     * Role: ENUM-like via string default 'member'
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();                                          // PK: BIGINT UNSIGNED
            $table->string('name');                                // VARCHAR(255)
            $table->string('email')->unique();                     // VARCHAR(255), UNIQUE
            $table->string('phone')->unique()->nullable();         // VARCHAR(255), UNIQUE, NULLABLE
            $table->timestamp('email_verified_at')->nullable();    // TIMESTAMP, NULLABLE
            $table->string('password');                            // VARCHAR(255)
            $table->string('role')->default('member');             // VARCHAR(255), DEFAULT 'member'
            $table->rememberToken();                               // VARCHAR(100), NULLABLE
            $table->timestamps();                                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
