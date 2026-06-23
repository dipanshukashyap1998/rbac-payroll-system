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
