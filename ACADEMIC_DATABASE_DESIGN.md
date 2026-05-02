# Academic Database Design — Noble Library System

> **3NF-Compliant Relational Database with Advanced SQL Programmability**

---

## 1. Normalization Analysis

### First Normal Form (1NF) ✅
- All tables contain only **atomic values** — no repeating groups.
- Author names were previously stored as comma-separated strings in the `books` table. This has been resolved by extracting authors into a dedicated `authors` table and using a **Many-to-Many pivot** (`book_author`).
- Each column contains a single value of a consistent data type.

### Second Normal Form (2NF) ✅
- All non-key attributes are **fully functionally dependent** on the entire primary key.
- The only composite key is `book_author(book_id, author_id)` — this junction table has no non-key attributes, so 2NF is automatically satisfied.
- All other tables use single-column surrogate keys (`id`), so partial dependency is impossible.

### Third Normal Form (3NF) ✅
- No **transitive dependencies** exist in any table.
- The previously redundant `available` column has been **removed** from the `books` table. It was a derived attribute (`stock - COUNT(active_borrows)`) that violated 3NF by depending on data in the `transactions` table. Availability is now computed dynamically via a **LEFT JOIN subquery** at query time.
- Category data is stored in a separate `categories` table, referenced via `category_id` FK — eliminating the transitive dependency of category name on book ID.

---

## 2. Entity Relationship Diagram (ERD)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│   users     │       │   books      │       │  categories │
├─────────────┤       ├──────────────┤       ├─────────────┤
│ PK: id      │       │ PK: id       │       │ PK: id      │
│ name        │       │ title        │  FK   │ name        │
│ email (UQ)  │       │ isbn (UQ)    │◄──────│ timestamps  │
│ phone (UQ)  │       │ FK:category_id│       └─────────────┘
│ password    │       │ image        │
│ role        │       │ stock        │       ┌─────────────┐
│ timestamps  │       │ timestamps   │       │  authors    │
└──────┬──────┘       └──────┬───────┘       ├─────────────┤
       │                     │               │ PK: id      │
       │ 1:M                 │ M:N           │ name (UQ)   │
       │                     │               │ bio         │
       ▼                     ▼               │ timestamps  │
┌──────────────┐     ┌───────────────┐       └──────┬──────┘
│ transactions │     │  book_author  │              │
├──────────────┤     ├───────────────┤              │
│ PK: id       │     │ PK,FK:book_id │──────────────┘
│ FK: user_id  │     │ PK,FK:author_id│
│ FK: book_id  │     └───────────────┘
│ borrowed_at  │
│ due_date     │
│ returned_at  │       ┌─────────────────┐
│ status       │       │  user_devices   │
│ notes        │       ├─────────────────┤
│ timestamps   │       │ PK: id          │
└──────┬───────┘       │ FK: user_id     │
       │               │ device_name     │
       │ 1:1           │ token (UQ)      │
       │               │ ip_address      │
       ▼               │ last_active_at  │
┌──────────────┐       │ timestamps      │
│    fines     │       └─────────────────┘
├──────────────┤
│ PK: id       │
│ FK:trans_id  │
│ amount       │
│ status       │
│ paid_at      │
│ timestamps   │
└──────────────┘
```

### Relationship Summary

| Relationship | Type | Description |
|---|---|---|
| `users` → `transactions` | One-to-Many | A user can borrow multiple books |
| `books` → `transactions` | One-to-Many | A book can appear in multiple transactions |
| `categories` → `books` | One-to-Many | A category can contain multiple books |
| `books` ↔ `authors` | Many-to-Many | Via `book_author` pivot table |
| `transactions` → `fines` | One-to-One | A transaction can have at most one fine |
| `users` → `user_devices` | One-to-Many | A user can have multiple logged-in devices |

---

## 3. Table Definitions with Data Types

### `users`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL |
| `email` | VARCHAR(255) | NOT NULL, UNIQUE |
| `phone` | VARCHAR(255) | NULLABLE, UNIQUE |
| `email_verified_at` | TIMESTAMP | NULLABLE |
| `password` | VARCHAR(255) | NOT NULL |
| `role` | VARCHAR(255) | NOT NULL, DEFAULT 'member' |
| `remember_token` | VARCHAR(100) | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

### `categories`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL, UNIQUE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

### `authors`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `name` | VARCHAR(255) | NOT NULL, UNIQUE |
| `bio` | TEXT | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

### `books`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `title` | VARCHAR(255) | NOT NULL |
| `isbn` | VARCHAR(255) | NOT NULL, UNIQUE |
| `category_id` | BIGINT UNSIGNED | FK → categories.id, NULLABLE, ON DELETE SET NULL |
| `image` | VARCHAR(255) | NULLABLE |
| `stock` | INT | NOT NULL, DEFAULT 1 |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

> **3NF Note:** The `available` column has been intentionally removed. Availability is computed as `stock - COUNT(active_borrows)` via a LEFT JOIN subquery. See Section 5 for the query.

### `book_author` (Pivot Table)
| Column | Type | Constraints |
|---|---|---|
| `book_id` | BIGINT UNSIGNED | PK (composite), FK → books.id, ON DELETE CASCADE |
| `author_id` | BIGINT UNSIGNED | PK (composite), FK → authors.id, ON DELETE CASCADE |

### `transactions`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | FK → users.id, ON DELETE CASCADE |
| `book_id` | BIGINT UNSIGNED | FK → books.id, ON DELETE CASCADE |
| `borrowed_at` | TIMESTAMP | NOT NULL, DEFAULT CURRENT_TIMESTAMP |
| `due_date` | DATE | NOT NULL |
| `returned_at` | TIMESTAMP | NULLABLE |
| `status` | ENUM('borrowed','returned','overdue','lost') | NOT NULL, DEFAULT 'borrowed' |
| `notes` | TEXT | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

### `fines`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `transaction_id` | BIGINT UNSIGNED | FK → transactions.id, ON DELETE CASCADE |
| `amount` | DECIMAL(8,2) | NOT NULL |
| `status` | ENUM('unpaid','paid') | NOT NULL, DEFAULT 'unpaid' |
| `paid_at` | TIMESTAMP | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

### `user_devices`
| Column | Type | Constraints |
|---|---|---|
| `id` | BIGINT UNSIGNED | PK, AUTO_INCREMENT |
| `user_id` | BIGINT UNSIGNED | FK → users.id, ON DELETE CASCADE |
| `device_name` | VARCHAR(255) | NOT NULL |
| `token` | VARCHAR(64) | NOT NULL, UNIQUE |
| `ip_address` | VARCHAR(45) | NULLABLE |
| `last_active_at` | TIMESTAMP | NULLABLE |
| `created_at` | TIMESTAMP | NULLABLE |
| `updated_at` | TIMESTAMP | NULLABLE |

---

## 4. Advanced SQL Features

### Stored Procedure: `ProcessLoan`

Handles the atomic "borrow a book" operation with pessimistic locking and JOIN-based availability checking.

```sql
DELIMITER //
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

    -- JOIN-based availability check (3NF compliant)
    SELECT COUNT(*) INTO v_active_borrows
    FROM transactions
    WHERE book_id = p_book_id
      AND status IN ('borrowed', 'overdue');

    IF v_stock > v_active_borrows THEN
        INSERT INTO transactions (user_id, book_id, borrowed_at, due_date, status, created_at, updated_at)
        VALUES (p_user_id, p_book_id, NOW(), p_due_date, 'borrowed', NOW(), NOW());
        COMMIT;
    ELSE
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Book is not available for borrowing.';
    END IF;
END //
DELIMITER ;
```

### Trigger: `CalculateLateFine`

Automatically calculates and inserts a fine record when a book is returned late.

```sql
DELIMITER //
CREATE TRIGGER CalculateLateFine
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
    IF NEW.returned_at IS NOT NULL AND OLD.returned_at IS NULL THEN
        SET @days_late = DATEDIFF(DATE(NEW.returned_at), NEW.due_date);
        IF @days_late > 0 THEN
            INSERT INTO fines (transaction_id, amount, status, created_at, updated_at)
            VALUES (NEW.id, @days_late * 1000.00, 'unpaid', NOW(), NOW());
        END IF;
    END IF;
END //
DELIMITER ;
```

---

## 5. JOIN Query: Dynamic Availability Computation

The availability of each book is computed via a **LEFT JOIN subquery** rather than stored redundantly:

```sql
-- Raw SQL equivalent of the Eloquent withAvailability() scope
SELECT
    books.*,
    books.stock - COALESCE(active.cnt, 0) AS available
FROM books
LEFT JOIN (
    SELECT book_id, COUNT(*) AS cnt
    FROM transactions
    WHERE status IN ('borrowed', 'overdue')
    GROUP BY book_id
) AS active ON books.id = active.book_id;
```

**Eloquent Implementation:**
```php
// In Book model — scopeWithAvailability()
Book::withCount(['activeBorrows as active_borrows_count'])->get();

// Accessor computes: $book->available = $book->stock - $book->active_borrows_count
```

---

## 6. Data Type Summary

| Purpose | Type Used | Example |
|---|---|---|
| Primary Keys | `BIGINT UNSIGNED` | `users.id`, `books.id` |
| Foreign Keys | `BIGINT UNSIGNED` | `books.category_id`, `transactions.user_id` |
| Strings | `VARCHAR(255)` | `users.name`, `books.title` |
| Long Text | `TEXT` | `authors.bio`, `transactions.notes` |
| Currency | `DECIMAL(8,2)` | `fines.amount` |
| Dates (deadlines) | `DATE` | `transactions.due_date` |
| Timestamps | `TIMESTAMP` | `transactions.borrowed_at` |
| Status flags | `ENUM` | `transactions.status`, `fines.status` |
| Boolean-like | `ENUM('unpaid','paid')` | `fines.status` |
