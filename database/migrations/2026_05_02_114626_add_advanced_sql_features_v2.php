<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Stored Procedure: ReturnBook
        // Handles the return process atomically: sets returned_at and updates status.
        // This triggers the CalculateLateFine trigger automatically.
        DB::unprepared("
            DROP PROCEDURE IF EXISTS ReturnBook;
            CREATE PROCEDURE ReturnBook(
                IN p_transaction_id BIGINT
            )
            BEGIN
                UPDATE transactions 
                SET returned_at = NOW(), 
                    status = 'returned',
                    updated_at = NOW()
                WHERE id = p_transaction_id;
            END;
        ");

        // 2. Trigger: PreventDuplicateBorrow
        // Prevents a user from borrowing the same book if they already have an active loan for it.
        DB::unprepared("
            DROP TRIGGER IF EXISTS PreventDuplicateBorrow;
            CREATE TRIGGER PreventDuplicateBorrow
            BEFORE INSERT ON transactions
            FOR EACH ROW
            BEGIN
                DECLARE v_count INT;
                SELECT COUNT(*) INTO v_count
                FROM transactions
                WHERE user_id = NEW.user_id 
                  AND book_id = NEW.book_id
                  AND status IN ('borrowed', 'overdue');
                
                IF v_count > 0 THEN
                    SIGNAL SQLSTATE '45001'
                        SET MESSAGE_TEXT = 'You already have an active loan for this book.';
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS ReturnBook;");
        DB::unprepared("DROP TRIGGER IF EXISTS PreventDuplicateBorrow;");
    }
};
