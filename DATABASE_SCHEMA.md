# Noble Library System - Database Schema Documentation

This document provides a comprehensive breakdown of the database architecture. The system uses a relational database structure designed for data integrity, performance, and security.

---

## Entity Relationship Summary
The database centers around three main entities: **Users**, **Books**, and **Transactions**.
- A **User** can have many **Transactions**.
- A **Book** can have many **Transactions**.
- A **User** can have many **UserDevices** (for security tracking).

---

## 1. `users` Table
Stores all account information for patrons and administrators.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Primary key. |
| `name` | VARCHAR | Full name of the user. |
| `email` | VARCHAR (Unique) | Primary login identifier. |
| `phone` | VARCHAR (Unique) | Secondary login identifier. |
| `password` | VARCHAR | BCRYPT hashed password. |
| `role` | ENUM | `'admin'` or `'member'`. |
| `remember_token` | VARCHAR | Session persistence token. |
| `created_at` | TIMESTAMP | Account creation date. |

---

## 2. `books` Table
Stores the library catalog and inventory levels.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Primary key. |
| `isbn` | VARCHAR (Unique) | ISBN-10 or ISBN-13 identifier. |
| `title` | VARCHAR | Title of the book. |
| `author` | VARCHAR | Author(s) of the book. |
| `category` | VARCHAR | Genre (Fiction, History, None, etc.). |
| `image` | VARCHAR | Path to the cover image file. |
| `stock` | INT | Total copies owned by the library. |
| `available` | INT | Copies currently available for borrowing. |
| `created_at` | TIMESTAMP | When the book was added to catalog. |

---

## 3. `transactions` Table
The core log of all library activity (Borrowing, Returning, Losses).

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Primary key. |
| `user_id` | BIGINT (FK) | Links to `users.id`. |
| `book_id` | BIGINT (FK) | Links to `books.id`. |
| `borrowed_at` | DATETIME | Timestamp of when the book was taken. |
| `due_date` | DATE | Expected return date. |
| `returned_at` | DATETIME | Timestamp of actual return (NULL if not returned). |
| `status` | ENUM | `'borrowed'`, `'returned'`, `'overdue'`, `'lost'`. |
| `notes` | TEXT | Administrative comments or damage reports. |

---

## 4. `user_devices` Table
Tracks active sessions for security and remote revocation.

| Column | Type | Description |
| :--- | :--- | :--- |
| `id` | BIGINT (PK) | Primary key. |
| `user_id` | BIGINT (FK) | Links to `users.id`. |
| `device_token` | VARCHAR | Unique identifier for the current session. |
| `ip_address` | VARCHAR | IP address used during the session. |
| `last_active` | TIMESTAMP | For cleanup and security auditing. |

---

## 5. Security & System Tables
- **`migrations`**: Tracks the version history of the database schema.
- **`sessions`**: (Optional) Stores server-side session data if `SESSION_DRIVER=database`.
- **`password_reset_tokens`**: Manages temporary tokens for password recovery.

---

## 6. Performance & Concurrency
To handle high-traffic environments, the database implementation includes:
- **Pessimistic Locking**: The `borrow` method in `CatalogController` uses `lockForUpdate()` on the `books` table. This prevents "phantom reads" where two users might see the same book as available before it is decremented.
- **Atomic Transactions**: All inventory changes are wrapped in `DB::transaction()`. If the creation of a record in the `transactions` table fails, the `books.available` count is automatically rolled back to maintain perfect data integrity.

---

© 2026 Noble Library System - Database Architecture
