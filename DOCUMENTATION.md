# Noble Library System - Technical Documentation

Welcome to the official documentation for the **Noble Library Management System**. This document provides an in-depth look at the architecture, features, and operational guidelines for administrators and developers.

---

## 1. System Architecture

The system is built on **Laravel 13**, utilizing a modern monolithic architecture with a focus on high-performance server-side rendering using Blade and Tailwind CSS.

### Key Components:
- **Middleware Layer**: Custom security layers including `SecurityHeaders` (CSP, XSS, HSTS) and `CheckDeviceToken` (Remote session revocation).
- **Service Layer**: ISBN metadata fetching is handled via the **Open Library API** integrated directly into the administrative controllers.
- **Frontend**: A custom-built design system using **Vanilla Tailwind CSS**, optimized for mobile-first accessibility.

---

## 2. Advanced Features

### 🛡️ Enterprise Security
- **Remote Session Revocation**: Every login generates a unique device token. Administrators can invalidate these tokens in the database to instantly force-logout compromised accounts without affecting other users.
- **Brute Force Protection**: Implemented via Laravel's rate limiting on the login route (max 5 attempts per minute per IP/Email).
- **Identity-Based Login**: Patrons can log in using either their **Email** or **Phone Number**.

### 📚 ISBN Automation
When an administrator enters an ISBN-10 or ISBN-13:
1. The system queries `openlibrary.org`.
2. Metadata (Title, Authors, Subjects) is parsed.
3. Subjects are automatically mapped to internal categories (Fiction, History, Science, etc.).
4. A high-quality cover image is fetched and displayed.

### 📋 Inventory & Stock Logic
- **Stock vs. Available**: 
  - `Stock` represents the total physical copies owned.
  - `Available` represents copies currently on the shelf.
- **Lost Book Workflow**: Marking a book as "Lost" reduces the `Stock` permanently. Reverting a "Lost" book back to "Returned" automatically reconciles the inventory.

---

## 3. Technical Optimizations

### ⚡ ISBN Metadata Caching
To optimize administrative performance, the system implements a **File-Based Cache** for Open Library API responses.
- **TTL**: 24 Hours.
- **Benefit**: Reduces external network latency and avoids API rate-limiting. Once a book is looked up once, subsequent searches are near-instant.

### 🛡️ Atomic Borrowing (Race Condition Prevention)
The system uses **Database Transactions** combined with **Pessimistic Locking** (`SELECT ... FOR UPDATE`) during the borrowing process.
- **Mechanism**: When a patron initiates a borrow request, the specific book row is locked at the database level.
- **Benefit**: Ensures that two patrons cannot borrow the same "last copy" of a book simultaneously, maintaining 100% accurate inventory counts even under high concurrency.

---

## 4. Administrative Guide

### Managing Catalog
- **Add Book**: Use the search icon in the ISBN field to auto-fill details. Use the "Drag & Drop" zone for manual cover uploads.
- **Edit Book**: Update stock levels or categories.

### Monitoring Logs
- **Borrowing History**: Use the search bar to find logs by Name, Title, or ISBN.
- **Status Toggles**: Click the **Edit (Pencil)** icon on any log to change status (Borrowed, Returned, Overdue, Lost) or add administrative notes.

### Member Management
- **Patron Audits**: View a member's 10 most recent transactions directly on their "Edit" page to monitor reliability.

---

## 4. Database Schema (Highlights)

| Table | Purpose |
| :--- | :--- |
| `users` | Stores accounts with `role` (admin/member) and `phone`. |
| `books` | Stores catalog metadata, `isbn`, `stock`, and `available` counts. |
| `transactions` | Records the lifecycle of a loan, including `notes` and `returned_at`. |
| `user_devices` | Tracks active sessions and IP addresses for remote logout. |

---

## 5. Development Guidelines

### Adding New Categories
If you add a new book category (e.g., "Manga"), update the mapping logic in `App\Http\Controllers\Admin\BookController@lookup` to include relevant keywords for the automated ISBN lookup.

### Styling
Avoid using arbitrary Tailwind values (e.g., `text-[#123456]`). Instead, use the defined theme colors:
- **Navy**: Primary branding.
- **Noble-bg**: Light background shade.
- **Silver**: Border and secondary accents.

---

## 6. Troubleshooting

- **ISBN Lookup Failing**: Ensure the server has outbound internet access to `openlibrary.org`.
- **Images Not Showing**: Run `php artisan storage:link` to ensure the public link exists.
- **Session Issues**: Check `SESSION_DRIVER` in `.env`. The remote logout feature is optimized for the `file` or `database` drivers.

---

© 2026 Noble Library System. Premium Library Management.
