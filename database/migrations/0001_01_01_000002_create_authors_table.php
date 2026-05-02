<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: authors
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * Unique: name
     * Purpose: Normalized entity (1NF) — eliminates repeating author strings in books.
     *          Enables Many-to-Many relationship with books via pivot table.
     */
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();                       // PK: BIGINT UNSIGNED
            $table->string('name')->unique();   // VARCHAR(255), UNIQUE
            $table->text('bio')->nullable();     // TEXT, NULLABLE
            $table->timestamps();               // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
