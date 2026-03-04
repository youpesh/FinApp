# 💼 Smart Finance - Web-Based Accounting Software

A comprehensive, modern accounting software system built with Laravel 11, featuring role-based access control, chart of accounts management, journal entry workflows, financial reporting, and advanced analytics.

![Laravel](https://img.shields.io/badge/Laravel-11.x-red?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.5-blue?style=flat-square&logo=php)
![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)

## 📋 Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Docker Deployment (Easiest)](#docker-deployment-easiest)
- [Default Credentials](#default-credentials)
- [Project Structure](#project-structure)
- [Sprint Progress](#sprint-progress)
- [Security Features](#security-features)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## ✨ Features

### Sprint 1: Authentication & User Management ✅ COMPLETED
- **Multi-Role System**: Three user types (Administrator, Manager, Accountant)
- **Advanced Password Security**:
  - Strong password validation (min 8 chars, must start with letter, requires letter, number, special char)
  - Password history tracking (prevents reuse)
  - 90-day password expiration with 3-day warning notifications
  - Password encryption using bcrypt
- **Account Security**:
  - Failed login attempt tracking (max 3 attempts)
  - Automatic account suspension after failed attempts
  - User activation/deactivation controls
  - Dated suspension with configurable start and end dates
- **User Management**:
  - Admin-controlled user creation and role assignment
  - User access request workflow with approval system
  - Username auto-generation (FirstInitial + LastName + MMYY)
  - Profile picture support
- **Administrative Reports**:
  - User Listing report with password expiry status indicators
  - Expired Passwords report with days-overdue count
- **Internal Email System**:
  - Admin-to-user email composer
  - Per-user email history log
  - Emails persisted in `email_logs` table
- **Activity Logging**: System-wide audit trail UI with before/after change tracking

### Sprint 2: Chart of Accounts (Planned)
- CRUD operations for accounts (Admin only)
- Account validation and categorization
- Event logging for account changes
- Search and filtering functionality
- Monetary value formatting

### Sprint 3: Journal Entries & Ledger (Planned)
- Multi-debit/credit journal entry creation
- Source document attachments (PDF, Word, Excel, CSV, JPG, PNG)
- Manager approval workflow
- Account ledger with clickable post references
- Balance calculations and reporting

### Sprint 4: Financial Reports (Planned)
- Trial Balance generation
- Income Statement
- Balance Sheet
- Retained Earnings Statement
- PDF export and email functionality

### Sprint 5: Dashboard & Analytics (Planned)
- Financial ratio calculations with color-coded indicators
- Role-specific dashboards
- Pending approval notifications
- Comprehensive help system

## 🛠 Tech Stack

- **Backend**: Laravel 11.x
- **Frontend**: Blade Templates + Alpine.js + Tailwind CSS
- **Database**: SQLite (development) / MySQL/PostgreSQL (production)
- **Authentication**: Laravel Breeze
- **Authorization**: Spatie Laravel Permission
- **Activity Logging**: Spatie Laravel Activity Log
- **PDF Generation**: DomPDF
- **Image Processing**: Intervention Image

## 📦 Requirements

- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite/MySQL/PostgreSQL

## 🚀 Installation

### 1. Clone the Repository

```bash
git clone https://github.com/youpesh/FinApp.git
cd FinApp
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install NPM dependencies
npm install
```

### 3. Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
# For SQLite (default):
DB_CONNECTION=sqlite

# For MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=finapp
# DB_USERNAME=root
# DB_PASSWORD=
```

### 4. Database Setup

```bash
# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations and seed database
php artisan migrate:fresh --seed
```

### 5. Storage Setup

```bash
# Create storage symlink
php artisan storage:link
```

### 6. Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 7. Start Development Server

```bash
php artisan serve
```

Visit `http://127.0.0.1:8000` in your browser.

## 🐳 Docker Deployment (Easiest)

For a quick demo on any server, use Docker. This bundles PHP, Node, and the database into containers.

### 1. Requirements
Ensure **Docker** and **Docker Compose** are installed on your server.

### 2. Setup & Start
Run these commands in the project root:

```bash
# Copy environment file
cp .env.example .env

# Start the containers
./vendor/bin/sail up -d

# Initialize the application
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan storage:link
./vendor/bin/sail npm run build
```

The application will be available at `http://your-server-ip`.


### 8. Set Up Scheduled Tasks (Optional)

For password expiry notifications, add this to your crontab:

```bash
* * * * * cd /path-to-finapp && php artisan schedule:run >> /dev/null 2>&1
```

## 🔑 Default Credentials

The seeder creates three test accounts:

| Role        | Email                 | Password     |
|-------------|-----------------------|--------------|
| Admin       | admin@finapp.com      | Admin123!    |
| Manager     | manager@finapp.com    | Manager123!  |
| Accountant  | accountant@finapp.com | Account123!  |

**⚠️ Important**: Change these passwords immediately in production!

## 📁 Project Structure

```
Smart Finance/
├── app/
│   ├── Console/Commands/          # Artisan commands (password expiry notifications)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/            # Admin-only controllers
│   │   │   │   ├── UserController.php
│   │   │   │   ├── ReportController.php      # User & expired-password reports
│   │   │   │   ├── EmailController.php       # Internal email compose/history
│   │   │   │   └── ActivityLogController.php # Audit trail viewer
│   │   │   └── Auth/             # Authentication controllers
│   │   ├── Middleware/           # Custom middleware (roles, status checks)
│   │   └── Requests/             # Form request validation
│   ├── Models/                   # Eloquent models (User, EmailLog, PasswordHistory)
│   ├── Notifications/            # Email/database notifications
│   ├── Rules/                    # Custom validation rules (StrongPassword)
│   └── Services/                 # Business logic services (PasswordService)
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/                  # Database seeders
├── resources/
│   └── views/
│       ├── admin/
│       │   ├── users/            # User management views
│       │   ├── reports/          # User & expired-password reports
│       │   ├── emails/           # Compose & history views
│       │   └── activity-logs/    # Audit trail view
│       └── auth/                 # Login, register, password reset
└── routes/
    ├── web.php                   # Web routes (admin, profile)
    └── auth.php                  # Authentication routes
```

## 📊 Sprint Progress

- [x] **Sprint 1**: User Interface & Authentication Module ✅
  - [x] Multi-role authentication (Admin, Manager, Accountant)
  - [x] Password security & management (complexity, history, 90-day expiry)
  - [x] User management dashboard (create, edit, suspend, delete)
  - [x] Dated user suspension with start/end date pickers
  - [x] Activity logging with admin audit trail UI
  - [x] Password expiry notifications (3-day warning)
  - [x] Administrative reports (User Listing, Expired Passwords)
  - [x] Internal email system (compose + per-user history)
  - [x] All 25 automated tests passing
  
- [ ] **Sprint 2**: Chart of Accounts Module
- [ ] **Sprint 3**: Journalizing & Ledger Module
- [ ] **Sprint 4**: Adjusting Entries & Financial Reports
- [ ] **Sprint 5**: Dashboard & Financial Ratios

## 🔒 Security Features

- ✅ **Password Security**:
  - Bcrypt hashing
  - Password complexity requirements
  - Password history tracking
  - Automatic expiration (90 days)
  
- ✅ **Access Control**:
  - Role-based permissions (Admin, Manager, Accountant)
  - User status verification (active, inactive, suspended, pending)
  - Failed login attempt tracking
  - Automatic account suspension
  
- ✅ **Audit Trail**:
  - Complete activity logging
  - Before/after data snapshots
  - IP address tracking
  - User action timestamps

- ✅ **Laravel Security**:
  - CSRF protection
  - SQL injection prevention (Eloquent ORM)
  - XSS protection (Blade templating)
  - Session security

## 📖 Usage

### For Administrators

1. **User Management**:
   - Create new users and assign roles
   - Approve or deny user access requests
   - Activate, deactivate, or terminate users
   - Suspend users with optional dated start/end periods

2. **Administrative Tools**:
   - View User Listing report with password expiry indicators
   - View Expired Passwords report
   - Send internal emails to users
   - Review system-wide activity audit trail

3. **System Monitoring**:
   - Monitor login attempts and account lockouts
   - Receive password expiry notifications

### For Managers

1. **Approval Workflow**:
   - Review and approve journal entries
   - View pending approvals
   - Generate financial reports

### For Accountants

1. **Daily Operations**:
   - Create journal entries
   - Attach source documents
   - View account ledgers
   - Submit entries for approval

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👥 Authors

- **Youpesh Bukhari** - *Initial work* - [youpesh](https://github.com/youpesh)

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- UI components from [Tailwind CSS](https://tailwindcss.com)
- Authentication scaffolding by [Laravel Breeze](https://laravel.com/docs/breeze)
- Activity logging by [Spatie](https://spatie.be)

## 📞 Support

For support, email admin@finapp.com or open an issue on GitHub.

---

**⭐ If you find this project useful, please consider giving it a star!**
