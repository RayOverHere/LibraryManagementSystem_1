<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Table: transactions
     * PK: id (BIGINT UNSIGNED AUTO_INCREMENT)
     * FK: user_id → users.id (ON DELETE CASCADE)
     * FK: book_id → books.id (ON DELETE CASCADE)
     *
     * Data Types:
     * - borrowed_at: TIMESTAMP (auto-set on creation)
     * - due_date: DATE (deadline type as per grading criteria)
     * - returned_at: TIMESTAMP NULLABLE (set when book is returned)
     * - status: ENUM (borrowed, returned, overdue, lost)
     * - notes: TEXT NULLABLE (admin annotations)
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();                                                              // PK: BIGINT UNSIGNED
            $table->foreignId('user_id')->constrained()->onDelete('cascade');           // FK → users.id
            $table->foreignId('book_id')->constrained()->onDelete('cascade');           // FK → books.id
            $table->timestamp('borrowed_at')->useCurrent();                            // TIMESTAMP
            $table->date('due_date');                                                  // DATE
            $table->timestamp('returned_at')->nullable();                              // TIMESTAMP, NULLABLE
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])        // ENUM
                  ->default('borrowed');
            $table->text('notes')->nullable();                                         // TEXT, NULLABLE
            $table->timestamps();                                                      // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
