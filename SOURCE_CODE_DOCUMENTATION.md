# IPCR/OPCR Management System — Source Code Documentation

**System Title:** IPCR/OPCR Management System  
**Version:** 10  
**Framework:** Laravel 12 (PHP 8.2)  
**Frontend:** Blade Templates, Vite, Tailwind CSS 4, Hotwire Turbo  
**Database:** MySQL  
**Date Generated:** May 19, 2026  

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [Technology Stack](#2-technology-stack)
3. [Project Directory Structure](#3-project-directory-structure)
4. [User Roles & Permissions](#4-user-roles--permissions)
5. [Core Modules](#5-core-modules)
6. [Database Layer](#6-database-layer)
7. [Backend Source Files](#7-backend-source-files)
8. [Frontend Source Files](#8-frontend-source-files)
9. [Configuration & Infrastructure](#9-configuration--infrastructure)
10. [Route Definitions](#10-route-definitions)

---

## 1. System Overview

This system is a web-based application for managing Individual Performance Commitment and Review (IPCR) and Office Performance Commitment and Review (OPCR) workflows within a university setting. It supports end-to-end performance cycle operations including template creation, submission management, dean review/calibration, HR summary reporting, and administrative controls.

### Key Features
- Role-based access control (Admin, HR, Dean, Director, Faculty)
- IPCR/OPCR template creation, editing, and activation
- Submission workflows with saved copies and version control
- Excel (.xlsx) import and export of IPCR/OPCR data
- Dean review and calibration of faculty/dean submissions
- HR summary reports with Excel export (faculty, staff, dean/director)
- Supporting document management for evidence files
- Administrative panel (user management, roles, notifications, database backup)
- Email verification and password reset via verification code
- Activity logging and audit trail
- Cloud storage integration (Cloudflare R2 / AWS S3)
- Profile photo management

---

## 2. Technology Stack

| Layer | Technology |
|-------|-----------|
| Backend Language | PHP 8.2 |
| Framework | Laravel 12 |
| Frontend Templating | Blade |
| CSS Framework | Tailwind CSS 4 |
| Build Tool | Vite 7 |
| SPA Navigation | Hotwire Turbo 8 |
| Database | MySQL |
| Spreadsheet | PhpSpreadsheet 5 |
| Image Processing | Intervention Image 3 |
| Cloud Storage | AWS S3 / Cloudflare R2 (via Flysystem) |
| Mail | Brevo SMTP |
| Testing | Pest 3 |
| Dev Tools | Laravel Pint, Pail, Sail, Boost |

### PHP Dependencies (composer.json)
- `laravel/framework` ^12.0
- `phpoffice/phpspreadsheet` ^5.5
- `intervention/image` ^3.11
- `league/flysystem-aws-s3-v3` 3.0
- Extensions: dom, gd, simplexml, xml, xmlreader, xmlwriter, zip

### Node Dependencies (package.json)
- `tailwindcss` ^4.0.0
- `vite` ^7.0.7
- `laravel-vite-plugin` ^2.0.0
- `@hotwired/turbo` ^8.0.23
- `axios` ^1.11.0

---

## 3. Project Directory Structure

```
ipcr_system_v10/
├── app/
│   ├── Console/Commands/          # Artisan CLI commands
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/             # Admin panel controllers
│   │       ├── Auth/              # Authentication controllers
│   │       ├── Dashboard/         # Dashboard controllers (Faculty, Dean, Director, Admin)
│   │       ├── Dean/              # Dean review/calibration controllers
│   │       └── Faculty/           # IPCR/OPCR template, submission, export controllers
│   ├── Middleware/                 # Role & permission middleware
│   ├── Models/                    # Eloquent ORM models
│   ├── Notifications/             # Email notification classes
│   ├── Providers/                 # Service providers
│   ├── Services/                  # Business logic services
│   └── Support/                   # Helper/utility classes
├── bootstrap/                     # Application bootstrapping
├── config/                        # Framework configuration files
├── database/
│   ├── factories/                 # Model factories
│   ├── migrations/                # Database migration files (40 migrations)
│   └── seeders/                   # Database seeders
├── public/                        # Publicly accessible files
│   ├── build/                     # Vite compiled assets
│   ├── images/                    # Static images
│   └── template/                  # Template files
├── resources/
│   ├── css/                       # Source CSS files (14 files)
│   ├── js/                        # Source JavaScript files (26 files)
│   └── views/
│       ├── admin/                 # Admin panel Blade views
│       ├── auth/                  # Login, password reset, verification views
│       ├── dashboard/             # Dashboard views (admin, dean, director, faculty)
│       ├── layouts/               # Layout templates
│       └── vendor/                # Vendor-published views
├── routes/                        # Route definitions
├── storage/                       # File storage, logs, cache
├── tests/                         # Automated tests
├── composer.json                  # PHP dependencies
├── package.json                   # Node dependencies
└── vite.config.js                 # Vite build configuration
```

---

## 4. User Roles & Permissions

| Role | Access Level | Key Permissions |
|------|-------------|-----------------|
| **Admin** | Superuser — bypasses all role checks | Full system access, user management, database management, activity logs, role management |
| **HR** | Human Resources | Summary reports, user management, notifications/deadlines, admin dashboard |
| **Dean** | College Dean | Faculty dashboard, OPCR workflows, faculty IPCR review & calibration, dean IPCR review |
| **Director** | Campus Director | Faculty dashboard, OPCR workflows, campus-wide monitoring |
| **Faculty** | Regular Faculty | IPCR workflows, profile management, supporting documents |

### Middleware Stack
- `auth` — Ensures user is authenticated
- `guest` — Ensures user is NOT authenticated
- `role:roleName` — Checks user role (supports comma-separated multiple roles)
- `permission:permName` — Checks specific permission

---

## 5. Core Modules

### 5.1 Authentication Module
- Login/logout with session management
- Password reset via email verification code (throttled)
- Email verification for new accounts

### 5.2 Faculty Dashboard Module
- Overview dashboard with statistics and notifications
- Profile management (personal info, password change, photo gallery)
- Notifications and deadlines display

### 5.3 IPCR Management Module
- **Templates** — Create, edit, delete, activate/deactivate IPCR templates
- **Submissions** — Submit, update, activate, deactivate, unsubmit IPCR forms
- **Saved Copies** — Save drafts, restore from saved copies
- **Import** — Import IPCR data from .xlsx files
- **Export** — Export IPCR data to .xlsx format

### 5.4 OPCR Management Module
- Same workflow as IPCR but for Dean/Director users
- Template, submission, saved copy, and export functionality
- Splitscreen comparison with approved IPCRs

### 5.5 Dean Review & Calibration Module
- View faculty IPCR submissions for review
- View dean IPCR submissions for review
- Save calibration scores
- Return submissions with feedback

### 5.6 Director Monitoring Module
- Campus-wide monitoring of IPCR/OPCR submissions
- View individual IPCR and OPCR submission details

### 5.7 HR Summary Reports Module
- Faculty summary reports with department filtering
- Staff summary reports
- Dean/Director summary reports with manual score overrides
- Export reports to Excel (individual and combined)
- View dean IPCR submissions

### 5.8 Admin Panel Module
- **User Management** — CRUD users, toggle active status, assign roles
- **Photo Management** — Upload, delete, set profile photos for users
- **Role & Department Management** — Manage roles, departments, designations
- **Notifications & Deadlines** — Create and manage system notifications and deadlines
- **Activity Logs** — View and export system activity logs
- **Database Management** — Backup, restore, download, upload database files

### 5.9 Supporting Documents Module
- Upload evidence/supporting files
- Download, rename, and delete documents
- Accessible by faculty and dean roles

---

## 6. Database Layer

### 6.1 Eloquent Models (app/Models/)

| File | Description |
|------|-------------|
| `User.php` | Core user model with role/permission relationships |
| `Role.php` | User roles with permission relationships |
| `UserRole.php` | Pivot model for user-role assignments |
| `Permission.php` | System permissions model |
| `Department.php` | Academic departments |
| `Designation.php` | User designations/titles |
| `IpcrTemplate.php` | IPCR template storage model |
| `IpcrSubmission.php` | IPCR submission records |
| `IpcrSavedCopy.php` | IPCR saved draft copies |
| `OpcrTemplate.php` | OPCR template storage model |
| `OpcrSubmission.php` | OPCR submission records |
| `OpcrSavedCopy.php` | OPCR saved draft copies |
| `DeanCalibration.php` | Dean calibration scores for submissions |
| `DeanDirectorSummaryOverride.php` | Manual score overrides for dean/director reports |
| `SupportingDocument.php` | Evidence file metadata |
| `UserPhoto.php` | User profile photo records |
| `ActivityLog.php` | System activity audit trail |
| `AdminNotification.php` | Admin-created notifications |
| `UpcomingDeadline.php` | System deadline entries |

### 6.2 Migrations (database/migrations/) — 40 Migration Files

| Migration | Purpose |
|-----------|---------|
| `create_departments_table` | Departments table |
| `create_designations_table` | Designations table |
| `create_users_table` | Core users table |
| `create_sessions_table` | Session storage |
| `create_cache_table` | Cache storage |
| `create_user_photos_table` | User photos |
| `create_user_roles_table` | User-role pivot |
| `add_employee_id_to_users_table` | Employee ID field |
| `add_last_login_at_to_users_table` | Login tracking |
| `create_ipcr_templates_table` | IPCR templates |
| `create_ipcr_submissions_table` | IPCR submissions |
| `create_password_reset_tokens_table` | Password reset tokens |
| `create_ipcr_saved_copies_table` | IPCR saved copies |
| `add_table_body_html_to_ipcr_templates` | HTML body for templates |
| `add_is_active_to_ipcr_templates` | Active flag (x2 migrations) |
| `add_so_count_json_to_ipcr_templates` | SO count JSON field |
| `add_active_and_so_count_to_ipcr_submissions` | Active + SO count for submissions |
| `create_opcr_tables` | OPCR templates, submissions, saved copies |
| `add_so_count_json_to_opcr_saved_copies` | SO count for OPCR copies |
| `create_email_verifications_table` | Email verification codes |
| `create_supporting_documents_table` | Supporting documents |
| `create_activity_logs_table` | Activity logs |
| `create_roles_table` | Roles table |
| `change_user_roles_role_to_string` | Role column type change |
| `create_permissions_table` | Permissions table |
| `create_role_permissions_table` | Role-permission pivot |
| `move_opcr_permissions_to_dean` | Permission reassignment |
| `create_admin_notifications_table` | Admin notifications |
| `fix_supporting_documents_mime_types` | MIME type fix |
| `add_noted_by_and_approved_by_to_ipcr_opcr` | Approval fields |
| `create_dean_calibrations_table` | Dean calibrations |
| `add_user_id_to_admin_notifications` | Notification user link |
| `create_notification_reads_table` | Notification read status |
| `add_employment_status_to_users` | Employment status field |
| `add_gso_department` | GSO department |
| `create_dean_director_summary_overrides` | Summary overrides |
| `convert_dean_director_overrides_to_percentage_points` | Data conversion |
| `remove_opcr_permissions_from_faculty` | Permission cleanup |
| `add_feedback_fields_to_dean_calibrations` | Feedback fields |

### 6.3 Seeders (database/seeders/)

| File | Description |
|------|-------------|
| `DatabaseSeeder.php` | Master seeder that calls all other seeders |
| `DepartmentSeeder.php` | Seeds default departments |
| `DesignationSeeder.php` | Seeds default designations |
| `UserSeeder.php` | Seeds test users (admin, dean, director, faculty, faculty2) |

---

## 7. Backend Source Files

### 7.1 Controllers (app/Http/Controllers/)

#### Authentication Controllers

| File | Description |
|------|-------------|
| `Auth/LoginController.php` | Login form display, authentication, logout |
| `EmailVerificationController.php` | Send and verify email verification codes |
| `PasswordResetController.php` | Forgot password, verify code, reset password flow |

#### Dashboard Controllers (app/Http/Controllers/Dashboard/)

| File | Description |
|------|-------------|
| `AdminDashboardController.php` | Admin dashboard index with system statistics |
| `DeanDashboardController.php` | Dean dashboard (redirects to faculty dashboard) |
| `DirectorDashboardController.php` | Director dashboard entry point |
| `DirectorMonitoringController.php` | Campus-wide IPCR/OPCR monitoring and detail views |
| `FacultyDashboardController.php` | Main faculty dashboard — index, my-ipcrs page, profile management, photo operations, notifications |

#### Dean Controllers (app/Http/Controllers/Dean/)

| File | Description |
|------|-------------|
| `DeanReviewController.php` | Faculty & dean submission review, calibration scoring, submission return with feedback |

#### Faculty Controllers (app/Http/Controllers/Faculty/)

| File | Description |
|------|-------------|
| `IpcrTemplateController.php` | CRUD for IPCR templates, activation, save/restore copies |
| `IpcrSubmissionController.php` | IPCR submission CRUD, activate, deactivate, unsubmit |
| `IpcrSavedCopyController.php` | IPCR saved copy CRUD |
| `IpcrExportController.php` | Export IPCR submissions/copies/templates to .xlsx |
| `IpcrImportController.php` | Import IPCR data from .xlsx files |
| `OpcrTemplateController.php` | CRUD for OPCR templates (Dean/Director) |
| `OpcrSubmissionController.php` | OPCR submission CRUD, activate, deactivate, unsubmit |
| `OpcrSavedCopyController.php` | OPCR saved copy CRUD |
| `OpcrExportController.php` | Export OPCR submissions/copies/templates to .xlsx |
| `SummaryReportController.php` | HR summary reports — faculty, staff, dean/director lists, exports, score overrides |
| `SupportingDocumentController.php` | Supporting document upload, download, rename, delete |

#### Admin Controllers (app/Http/Controllers/Admin/)

| File | Description |
|------|-------------|
| `UserManagementController.php` | User CRUD, toggle active status, JSON API |
| `PhotoController.php` | Admin photo upload, delete, set as profile |
| `RoleDesignationController.php` | Role, department, designation CRUD |
| `NotificationDeadlineController.php` | Notification and deadline CRUD, API endpoints |
| `ActivityLogController.php` | Activity log listing and export |
| `DatabaseManagementController.php` | Database backup, restore, download, upload, settings |

### 7.2 Middleware (app/Middleware/)

| File | Description |
|------|-------------|
| `RoleMiddleware.php` | Checks user roles; admin bypasses all checks; dean inherits faculty access |

### 7.3 Services (app/Services/)

| File | Description |
|------|-------------|
| `IpcrExportService.php` | IPCR Excel export logic with PhpSpreadsheet |
| `IpcrImportService.php` | IPCR Excel import parsing and validation |
| `OpcrExportService.php` | OPCR Excel export logic |
| `FacultySummaryExportService.php` | Faculty summary report Excel generation |
| `StaffSummaryExportService.php` | Staff summary report Excel generation |
| `DeanDirectorSummaryExportService.php` | Dean/Director summary report Excel generation |
| `ActivityLogService.php` | Activity logging business logic |
| `DatabaseBackupService.php` | Database backup/restore operations |
| `PhotoService.php` | Photo upload, storage, and management |
| `EmployeeIdService.php` | Employee ID generation and validation |
| `HtmlSanitizer.php` | HTML content sanitization for security |
| `SoLabelNormalizer.php` | Strategic Objective label normalization |

### 7.4 Notifications (app/Notifications/)

| File | Description |
|------|-------------|
| `EmailVerificationNotification.php` | Email verification code notification |
| `PasswordResetNotification.php` | Password reset code notification |

### 7.5 Support & Providers

| File | Description |
|------|-------------|
| `Support/MediaAsset.php` | Media asset helper for cloud storage URLs |
| `Providers/AppServiceProvider.php` | Application service provider |

### 7.6 Console Commands (app/Console/Commands/)

| File | Description |
|------|-------------|
| `MigrateCloudinaryToR2.php` | Migrate files from Cloudinary to Cloudflare R2 |
| `MigratePublicImagesToR2.php` | Migrate public images to R2 storage |
| `RunSystemBackup.php` | Automated system backup command |

---

## 8. Frontend Source Files

### 8.1 Blade Views (resources/views/)

#### Layout Templates

| File | Description |
|------|-------------|
| `layouts/admin.blade.php` | Admin panel layout with sidebar navigation |

#### Authentication Views (auth/)

| File | Description |
|------|-------------|
| `login.blade.php` | Login page |
| `forgot-password.blade.php` | Forgot password form |
| `verify-code.blade.php` | Verification code entry |
| `reset-password.blade.php` | Password reset form |

#### Admin Panel Views (admin/)

| File | Description |
|------|-------------|
| `users/index.blade.php` | User listing with search and filters |
| `users/edit.blade.php` | User edit form |
| `users/show.blade.php` | User detail view |
| `database/index.blade.php` | Database management interface |
| `activity-logs/index.blade.php` | Activity log viewer |
| `notifications/index.blade.php` | Notification/deadline management |
| `role-management/index.blade.php` | Role, department, designation management |

#### Dashboard Views (dashboard/)

| File | Description |
|------|-------------|
| `admin/index.blade.php` | Admin dashboard overview |
| `dean/index.blade.php` | Dean dashboard |
| `director/Index.blade.php` | Director dashboard |
| `director/monitoring.blade.php` | Director monitoring page |
| `director/monitoring-show.blade.php` | Director submission detail view |
| `faculty/index.blade.php` | Faculty main dashboard |
| `faculty/my-ipcrs.blade.php` | IPCR/OPCR management page |
| `faculty/profile.blade.php` | Faculty profile page |
| `faculty/summary-reports.blade.php` | HR summary reports page |
| `faculty/dean-ipcr-submission.blade.php` | Dean IPCR submission view |
| `faculty/partials/notifications-deadlines.blade.php` | Notifications partial |
| `faculty/partials/user-management.blade.php` | User management partial |

### 8.2 CSS Source Files (resources/css/) — 14 Files

| File | Description |
|------|-------------|
| `auth_login.css` | Login page styles |
| `auth_verify-code.css` | Verification code page styles |
| `auth_reset-password.css` | Password reset page styles |
| `dashboard_faculty_index.css` | Faculty dashboard styles |
| `dashboard_faculty_my-ipcrs.css` | My IPCRs page styles |
| `dashboard_faculty_profile.css` | Profile page styles |
| `dashboard_faculty_summary-reports.css` | Summary reports styles |
| `dashboard_admin_index.css` | Admin dashboard styles |
| `admin_layout.css` | Admin layout/sidebar styles |
| `admin_users_show.css` | User detail page styles |
| `admin_users_edit.css` | User edit page styles |
| `admin_users_index.css` | User listing styles |
| `admin_users_create.css` | User creation styles |
| `admin_database_index.css` | Database management styles |

### 8.3 JavaScript Source Files (resources/js/) — 26 Files

| File | Description |
|------|-------------|
| `auth_login.js` | Login form logic |
| `auth_verify-code.js` | Verification code handling |
| `auth_reset-password.js` | Password reset form logic |
| `dashboard_faculty_index.js` | Faculty dashboard initialization |
| `dashboard_faculty_index_page.js` | Faculty dashboard page-level logic |
| `dashboard_faculty_my-ipcrs.js` | My IPCRs initialization |
| `dashboard_faculty_my-ipcrs_page.js` | My IPCRs page logic (largest JS file — template editor, submissions, imports) |
| `dashboard_faculty_profile.js` | Profile management logic |
| `dashboard_faculty_summary-reports.js` | Summary reports logic |
| `dashboard_faculty_user-management.js` | User management partial logic |
| `dashboard_faculty_notifications-deadlines.js` | Notifications logic |
| `dashboard_faculty_dean-ipcr-submission.js` | Dean IPCR submission view logic |
| `dashboard_director_index.js` | Director dashboard logic |
| `dashboard_admin_index_page.js` | Admin dashboard page logic |
| `admin_layout.js` | Admin sidebar navigation logic |
| `admin_layout_theme.js` | Admin theme switcher |
| `admin_users_index.js` | User listing functionality |
| `admin_users_show.js` | User detail page logic |
| `admin_users_edit.js` | User edit form logic |
| `admin_users_create.js` | User creation form logic |
| `admin_database_index.js` | Database management logic |
| `admin_activity_logs_index.js` | Activity logs logic |
| `admin_notifications_index.js` | Notification management logic |
| `admin_role_management_index.js` | Role management logic |
| `tailwind_admin_config.js` | Tailwind config for admin panel |

---

## 9. Configuration & Infrastructure

### 9.1 Configuration Files (config/)

| File | Purpose |
|------|---------|
| `app.php` | Application name, environment, timezone, locale |
| `auth.php` | Authentication guards and providers |
| `cache.php` | Cache driver configuration |
| `database.php` | Database connection settings |
| `filesystems.php` | Local and cloud storage disk configuration |
| `logging.php` | Log channels and levels |
| `mail.php` | SMTP mail configuration (Brevo) |
| `queue.php` | Queue connection settings |
| `services.php` | Third-party service credentials |
| `session.php` | Session driver and lifetime |

### 9.2 Build & Deploy

| File | Purpose |
|------|---------|
| `vite.config.js` | Vite build config — input assets, build output, dev server |
| `composer.json` | PHP dependencies and scripts |
| `package.json` | Node dependencies and scripts |
| `nixpacks.toml` | Railway deployment configuration |
| `DEPLOYMENT_PLAN.md` | Step-by-step deployment guide |
| `.env.example` | Environment variable template |

---

## 10. Route Definitions

### Authentication Routes
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/` | LoginController@showLoginForm | login |
| POST | `/login` | LoginController@login | login.post |
| POST | `/logout` | LoginController@logout | logout |

### Password Reset Routes
| Method | URI | Controller | Name |
|--------|-----|-----------|------|
| GET | `/forgot-password` | PasswordResetController@showForgotPasswordForm | password.request |
| POST | `/forgot-password` | PasswordResetController@sendResetCode | password.email |
| GET/POST | `/verify-code` | PasswordResetController@showVerifyCodeForm / verifyCode | password.verify |
| GET/POST | `/reset-password` | PasswordResetController@showResetPasswordForm / resetPassword | password.update |

### Faculty Dashboard Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/faculty/dashboard` | faculty.dashboard |
| GET | `/faculty/my-ipcrs` | faculty.my-ipcrs |
| GET | `/faculty/profile` | faculty.profile |
| PATCH | `/faculty/profile/update` | faculty.profile.update |
| PATCH | `/faculty/password/change` | faculty.password.change |
| POST | `/faculty/profile/photo/upload` | faculty.profile.photo.upload |

### IPCR Template Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/faculty/ipcr/templates` | faculty.ipcr.templates.index |
| POST | `/faculty/ipcr/store` | faculty.ipcr.store |
| GET/PUT/DELETE | `/faculty/ipcr/templates/{id}` | Show / Update / Delete |
| POST | `/faculty/ipcr/templates/{id}/set-active` | Set Active |

### IPCR Submission Routes
| Method | URI | Name |
|--------|-----|------|
| POST | `/faculty/ipcr/submissions` | faculty.ipcr.submissions.store |
| GET/PUT/DELETE | `/faculty/ipcr/submissions/{id}` | Show / Update / Delete |
| POST | `/faculty/ipcr/submissions/{id}/set-active` | Set Active |
| POST | `/faculty/ipcr/submissions/{id}/deactivate` | Deactivate |
| POST | `/faculty/ipcr/submissions/{id}/unsubmit` | Unsubmit |

### OPCR Routes (Dean/Director)
Same pattern as IPCR routes under `/faculty/opcr/` prefix.

### Dean Review Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/dean/dashboard` | dean.dashboard |
| GET | `/dean/review/faculty-submissions` | dean.review.faculty-submissions |
| GET | `/dean/review/faculty-submissions/{id}` | dean.review.faculty-submission.show |
| GET | `/dean/review/dean-submissions` | dean.review.dean-submissions |
| POST | `/dean/review/calibrations` | dean.review.calibrations.save |
| POST | `/dean/review/calibrations/return` | dean.review.calibrations.return |

### Director Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/director/dashboard` | director.dashboard |
| GET | `/director/monitoring` | director.monitoring |
| GET | `/director/monitoring/ipcr/{submission}` | director.monitoring.ipcr.show |
| GET | `/director/monitoring/opcr/{submission}` | director.monitoring.opcr.show |

### HR Summary Report Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/faculty/summary-reports` | faculty.summary-reports |
| GET | `/faculty/summary-reports/faculty/export` | Export faculty summary |
| GET | `/faculty/summary-reports/staff/export` | Export staff summary |
| GET | `/faculty/summary-reports/dean-director/export` | Export dean/director summary |
| GET | `/faculty/summary-reports/export-all` | Export combined report |
| PUT | `/faculty/summary-reports/dean-director/{user}/scores` | Update scores |

### Admin Panel Routes
| Method | URI | Name |
|--------|-----|------|
| GET | `/admin/dashboard` | admin.dashboard |
| Resource | `/admin/panel/users` | admin.users.* |
| GET | `/admin/panel/database` | admin.database.index |
| POST | `/admin/panel/database/backup` | admin.database.backup |
| GET | `/admin/panel/activity-logs` | admin.activity-logs.index |
| GET | `/admin/panel/notifications` | admin.notifications.index |
| GET | `/admin/panel/role-management` | admin.role-management.index |

---

## File Count Summary

| Category | Count |
|----------|-------|
| Models | 19 |
| Controllers | 17 |
| Services | 12 |
| Middleware | 1 (custom) |
| Migrations | 40 |
| Seeders | 4 |
| Blade Views | ~25 |
| CSS Files | 14 |
| JavaScript Files | 26 |
| Console Commands | 3 |
| Notifications | 2 |
| Config Files | 10 |
| **Total Source Files** | **~173** |

---

*This document serves as a comprehensive source code listing and reference guide for the IPCR/OPCR Management System v10. All files are organized by their respective modules and layers within the Laravel MVC architecture.*
