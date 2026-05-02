<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: books
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * FK: category_id → categories.id (ON DELETE SET NULL)
     * Unique: isbn
     *
     * 3NF Compliance: The 'available' column has been REMOVED.
     * Previously, 'available' was a derived attribute computed from
     * stock - COUNT(active_transactions). Storing it violated 3NF
     * because it introduced a transitive dependency on external table data.
     * Availability is now computed dynamically via LEFT JOIN / subquery.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();                                          // PK: BIGINT UNSIGNED
            $table->string('title');                               // VARCHAR(255)
            $table->string('isbn')->unique();                      // VARCHAR(255), UNIQUE INDEX
            $table->foreignId('category_id')                       // FK → categories.id
                  ->nullable()
                  ->constrained('categories')
                  ->nullOnDelete();
            $table->string('image')->nullable();                   // VARCHAR(255), NULLABLE
            $table->integer('stock')->default(1);                  // INT, DEFAULT 1
            $table->timestamps();                                  // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
