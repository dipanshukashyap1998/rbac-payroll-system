# RBAC Payroll Product Guide

Welcome to RBAC Payroll. This guide explains the supported workflows for setting up a company, onboarding employees, managing leave, and viewing available payroll information.

> This guide describes the current application. Attendance entry, payable-day calculation, payroll creation, payroll processing, payment, and payslip generation are not currently available as user workflows.

## Getting Started

### Register an admin account

1. Open the application and select **Create an admin account** on the login page.
2. Enter your full name, email address, password, and password confirmation.
3. Use a password with at least 8 characters.
4. Submit the form.
5. Return to the login page and sign in with the new account.

Registration creates an active administrator account. A company must be created before the rest of the company workspace becomes available.

### Sign in

1. Enter the email address and password used during registration.
2. Select **Login**.
3. If you are an administrator without a company, the application redirects you to company setup.

Use **Logout** from the application header to end the current session.

## Set Up Your Company

Administrators can create one company from **Companies > Create Company**. Complete the required company name and status fields. You may also provide email, phone, city, state, and country.

After the company is created, the application seeds default leave types for the company, including earned, casual, sick, maternity, paternity, comp off, leave without pay, optional holiday, and bereavement leave.

If an administrator cannot access company modules, confirm that the administrator owns a company and has the required company permissions.

## Manage Users and Employees

### Add an employee account

Super administrators can create users from **Users > Create User**. To create an employee account:

1. Enter the employee name, email, password, and password confirmation.
2. Select the **employee** role.
3. Select the employee's company.
4. Optionally enter designation and joining date.
5. Submit the form.

Creating a user with the employee role creates the employee profile and initializes leave balances. This is the recommended onboarding path.

Administrators can maintain employee records from **Employees**, but that separate form attaches an existing user and currently has fewer onboarding safeguards. Use the user creation flow first when you are creating a new employee login.

### Roles and permissions

The available roles are **super_admin**, **admin**, and **employee**. Permissions control access to modules and actions. Role and permission management is restricted to super administrators.

Use **Roles > Permission Matrix** to review or synchronize permissions when your account has the required access. Employees should only receive the permissions needed for their work.

## Salary Structures

Administrators can create compensation templates from **Salary Structure > Create**. The available fields are name, base salary, HRA, allowances, and deductions.

Salary structures can be viewed by employees when an assigned structure exists. The current application does not provide a normal user interface for assigning a salary structure to an employee, so assignment may require an existing seeded or administrator-managed record.

## Leave Management

### Configure leave types

Administrators can manage leave types from **Leave Types**. A leave type can include a name, code, default days, paid/unpaid status, carry-forward settings, approval requirement, active status, and description.

### Submit a leave request

Employees can submit a request from **Leaves > Create Request**:

1. Select a leave type.
2. Choose the start and end dates.
3. For a same-day request, select the applicable session. Different start and end sessions count as a half day.
4. Add an optional reason.
5. Submit the request.

For multi-day requests, weekends are excluded. Paid leave is checked against the employee's available balance. Depending on the leave type, the request is either approved immediately or marked pending for administrator approval.

### Approve or reject leave

Administrators with leave approval permission can review pending requests from **Leaves**. Approve valid requests or reject them with an optional rejection reason. Leave balances and leave deductions are managed by the application's leave policy rules.

## Payslips and Payroll Information

Administrators can review available payroll runs from **Payrolls** and available payslips from **Payslips**. Employees can view their own released payslips when they have payslip access.

The current application does not provide forms or actions for creating a payroll run, calculating payroll, processing payroll, marking payroll paid, or generating payslips. Existing payroll and payslip entries may come from seed data or administrator-managed records.

## Attendance and Payable Days

Attendance records and payable-day calculation are not currently operational in the application interface. There is no supported attendance entry screen or payable-days workflow yet.

If you need payable-day support, contact your system administrator. Do not assume that a displayed payroll or payslip value was calculated from attendance unless your organization has separately configured that process.

## Troubleshooting

- **I cannot access the dashboard:** sign in again and confirm that your account has an assigned role and the required dashboard permission.
- **I am redirected to company setup:** your administrator account does not own a company yet.
- **I cannot create an employee:** confirm that you have employee creation permission, the selected company is available, and the email address is unique.
- **I cannot submit leave:** confirm that you have employee access, the leave type is active, and the paid leave balance is sufficient.
- **I cannot see a payslip:** confirm that the payslip is released and that your account has payslip permission.
- **I need payroll or attendance processing:** those workflows are not currently available through the application UI.

## Navigation Summary

The application navigation is permission-aware. Depending on your role, you may see Dashboard, Companies, Employees, Salary Structure, Leaves, Leave Types, Payrolls, Payslips, Audit Logs, Roles, Permissions, Users, and Logout.

The support chatbot can explain the workflows in this guide, but it cannot access private records, change application data, approve leave, process payroll, or calculate payable days.
