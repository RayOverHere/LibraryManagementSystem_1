<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Note: In MySQL/PostgreSQL we can change enum. In SQLite we might need a different approach.
            // Using change() to add 'lost' to the enum.
            $table->enum('status', ['borrowed', 'returned', 'overdue', 'lost'])->default('borrowed')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed')->change();
        });
    }
};
