<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates two advanced SQL programmability objects:
     *
     * 1. Stored Procedure: ProcessLoan
     *    - Handles the atomic "borrow a book" operation.
     *    - Uses SELECT ... FOR UPDATE (pessimistic locking) to prevent race conditions.
     *    - Computes availability via COUNT JOIN on transactions (no redundant column).
     *    - Raises SQLSTATE 45000 if book is unavailable.
     *
     * 2. Trigger: CalculateLateFine
     *    - Fires AFTER UPDATE on the transactions table.
     *    - When a book is returned (returned_at transitions from NULL to a value),
     *      it calculates overdue days using DATEDIFF.
     *    - Automatically inserts a fine record into the fines table.
     *    - Fine rate: Rp 1,000.00 per overdue day.
     */
    public function up(): void
    {
        // =====================================================================
        // STORED PROCEDURE: ProcessLoan
        // Demonstrates: Transactions, Locking, JOIN-based availability check
        // =====================================================================
        DB::unprepared("
            DROP PROCEDURE IF EXISTS ProcessLoan;
            CREATE PROCEDURE ProcessLoan(
                IN p_user_id BIGINT,
                IN p_book_id BIGINT,
                IN p_due_date DATE
            )
            BEGIN
                DECLARE v_stock INT;
                DECLARE v_active_borrows INT;

                START TRANSACTION;

                -- Lock the book row to prevent concurrent modifications
                SELECT stock INTO v_stock
                FROM books
                WHERE id = p_book_id
                FOR UPDATE;

                -- JOIN-based availability check (3NF compliant — no redundant column)
                SELECT COUNT(*) INTO v_active_borrows
                FROM transactions
                WHERE book_id = p_book_id
                  AND status IN ('borrowed', 'overdue');

                IF v_stock > v_active_borrows THEN
                    -- Book is available — create the loan record
                    INSERT INTO transactions (user_id, book_id, borrowed_at, due_date, status, created_at, updated_at)
                    VALUES (p_user_id, p_book_id, NOW(), p_due_date, 'borrowed', NOW(), NOW());

                    COMMIT;
                ELSE
                    -- Book is not available — abort
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Book is not available for borrowing.';
                END IF;
            END;
        ");

        // =====================================================================
        // TRIGGER: CalculateLateFine
        // Demonstrates: Automated business logic at the database level
        // =====================================================================
        DB::unprepared("
            DROP TRIGGER IF EXISTS CalculateLateFine;
            CREATE TRIGGER CalculateLateFine
            AFTER UPDATE ON transactions
            FOR EACH ROW
            BEGIN
                -- Fire only when a book is being returned (returned_at transitions from NULL)
                IF NEW.returned_at IS NOT NULL AND OLD.returned_at IS NULL THEN
                    SET @days_late = DATEDIFF(DATE(NEW.returned_at), NEW.due_date);
                    IF @days_late > 0 THEN
                        INSERT INTO fines (transaction_id, amount, status, created_at, updated_at)
                        VALUES (NEW.id, @days_late * 1000.00, 'unpaid', NOW(), NOW());
                    END IF;
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS ProcessLoan;");
        DB::unprepared("DROP TRIGGER IF EXISTS CalculateLateFine;");
    }
};
