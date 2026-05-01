# Noble Library Management System

An elegant, minimalist, and secure library management system built with Laravel 13 and Tailwind CSS. Featuring real-time session revocation, ISBN automation, and premium mobile-first UI.

## Key Features
- **Book Cataloging**: Automated metadata fetching using the **Open Library API**.
- **Security**: 
  - Real-time device session revocation.
  - Rate-limited authentication (Brute-force protection).
  - Enterprise-grade password policies.
  - Secure HTTP headers.
- **Member Management**: Detailed patron logs and history.
- **Performance Optimization**: 
  - 24-hour **ISBN Metadata Caching** for instant lookups.
  - **Pessimistic Locking** on borrowing transactions to prevent race conditions.
- **Inventory Control**: "Mark as Lost" workflow with automatic stock reconciliation.
- **Responsive UI**: Premium "Noble" design system with optimized mobile layouts.

## Requirements
- PHP 8.2 or higher
- Composer
- Node.js & NPM
- MySQL or SQLite

## Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone https://github.com/RayOverHere/LibraryManagementSystem_1.git
   cd LibraryManagementSystem_1
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**
   Copy the example environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   Edit your `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=library_system
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run Migrations & Symlink Storage**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

6. **Serve the Application**
   ```bash
   php artisan serve
   ```
   The application will be available at `http://localhost:8000`.

## Administrative Access
To create your first admin account, you can register a new account through the `/register` page and then manually update the `role` column to `admin` in your `users` database table.

## License
Open-sourced software licensed under the [MIT license](LICENSE).
