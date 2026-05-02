<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: categories
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * Unique: name
     * Purpose: Normalized entity (1NF) — eliminates repeating category strings in books.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();                       // PK: BIGINT UNSIGNED
            $table->string('name')->unique();   // VARCHAR(255), UNIQUE
            $table->timestamps();               // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
