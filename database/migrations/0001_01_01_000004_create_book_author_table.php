<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: book_author (Pivot / Junction Table)
     * Composite PK: (book_id, author_id)
     * FK: book_id → books.id (ON DELETE CASCADE)
     * FK: author_id → authors.id (ON DELETE CASCADE)
     *
     * Purpose: Resolves the Many-to-Many relationship between Books and Authors.
     * A book can have multiple authors, and an author can write multiple books.
     * This eliminates the repeating group violation (1NF) of storing
     * comma-separated author names in the books table.
     */
    public function up(): void
    {
        Schema::create('book_author', function (Blueprint $table) {
            $table->foreignId('book_id')        // FK → books.id
                  ->constrained()
                  ->cascadeOnDelete();
            $table->foreignId('author_id')      // FK → authors.id
                  ->constrained()
                  ->cascadeOnDelete();
            $table->primary(['book_id', 'author_id']);  // Composite PK
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_author');
    }
};
