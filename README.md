# RBAC Payroll System

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

A secure, multi-tenant corporate payroll management platform featuring Role-Based Access Control (RBAC). The application automates core salary calculations, manages dynamic Indian statutory compliance structures (PF, ESI, TDS, PT), and handles complex leave workflows such as Casual, Sick, and Earned Leaves with built-in Loss of Pay (LOP) automated proration.

---

## 🚀 Key Features

- **Dynamic Role-Based Access Control (RBAC):** Granular permissions for Super Admins, HR Managers, Finance/Payroll Operators, and Standard Employees.
- **Indian Statutory Compliance Engine:** Auto-calculation of PF, ESI, Professional Tax (PT variations across states), and TDS.
- **Leave & Attendance Tracking:** Dynamic accrual mechanisms, custom sandwich policy enforcement, and automatic LOP salary deductions.
- **Modern CI/CD Deployment:** Fully automated validation and zero-downtime symlink deployment using GitHub Actions.

---

## 🛠️ Local Installation Guide
- `Auto Merge Develop to Main` runs on every push to `develop`. It merges `develop` into `main` and pushes the result, which then triggers the `main` branch CI and deploy flow.
- `CI` runs on pushes to `main`, plus all pull requests targeting `develop` or `main`. It installs PHP dependencies and runs `php artisan test`.
- `Deploy` runs after a successful `main` branch CI run or manually from the Actions tab. It validates the release first, uploads the application to your server over SSH, runs `composer install --no-dev`, executes `php artisan migrate --force`, refreshes Laravel caches, and points the `current` symlink at the new release.

Set these repository or environment secrets before using deployment:

- `SSH_HOST`
- `SSH_PORT`
- `SSH_USER`
- `SSH_PRIVATE_KEY`
- `DEPLOY_PATH`
- `APP_ENV_FILE`

Expected server layout:

- The workflow creates `DEPLOY_PATH/releases`, `DEPLOY_PATH/shared`, and `DEPLOY_PATH/current`.
- `APP_ENV_FILE` is written to `DEPLOY_PATH/shared/.env`.
- Shared writable Laravel storage lives in `DEPLOY_PATH/shared/storage`.
=======
### Prerequisites
Ensure your local environment meets the standard requirements for Laravel 10/11:
- **PHP >= 8.2** (with BCMath, Ctype, cURL, DOM, Fileinfo, Mbstring, OpenSSL, PDO, Tokenizer, and XML extensions)
- **Composer**
- **Node.js & NPM**
- **MySQL >= 8.0** or PostgreSQL

### Setup Steps

1. **Clone the repository:**
   ```bash
   git clone [https://github.com/dipanshukashyap1998/rbac-payroll-system.git](https://github.com/dipanshukashyap1998/rbac-payroll-system.git)
   cd rbac-payroll-system
