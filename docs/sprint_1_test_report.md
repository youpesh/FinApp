# Sprint 1 Test Results

This report documents the testing results for Sprint 1 requirements of the Smart Finance application.

---

Test Case ID: TC-01
Test Name: User Login (Administrator)
Requirement Reference: Authentication
Preconditions: Admin account exists (admin@finapp.com)
Test Steps:
1. Navigate to the Login page
2. Enter email: admin@finapp.com
3. Enter password: Admin123!
4. Click Login
Expected Result:
User is redirected to the Dashboard with Admin privileges.
Status: Pass

---

Test Case ID: TC-02
Test Name: Create New User
Requirement Reference: User Management
Preconditions: User logged in as Admin
Test Steps:
1. Navigate to Users page (admin/users)
2. Click "Create User"
3. Enter First Name, Last Name, Email, and Role (Accountant)
4. Save
Expected Result:
New user record is created and appears in the user listing.
Status: Pass

---

Test Case ID: TC-03
Test Name: Create Account (Chart of Accounts)
Requirement Reference: Account Management
Preconditions: User logged in as Admin
Test Steps:
1. Navigate to Accounts page
2. Click Create Account
3. Enter account code, name, category, normal balance
4. Save
Expected Result:
New account appears in account list.
Status: Pass

---

Test Case ID: TC-04
Test Name: Password Complexity Validation
Requirement Reference: Advanced Password Security
Preconditions: User on password change or registration page
Test Steps:
1. Enter a password without a number (e.g., "Pass@word")
2. Enter a password that doesn't start with a letter (e.g., "1Password!")
3. Enter a password with valid complexity (e.g., "P@ssword123")
Expected Result:
System rejects weak passwords with specific error messages and accepts valid ones.
Status: Pass

---

Test Case ID: TC-05
Test Name: Account Lockout (Failed Attempts)
Requirement Reference: Account Security
Preconditions: Valid user account exists
Test Steps:
1. Attempt to login with the correct email but incorrect password 3 times
2. Attempt a 4th login with correct credentials
Expected Result:
System locks the account after 3 failed attempts; 4th attempt is denied even with correct password.
Status: Pass

---

Test Case ID: TC-06
Test Name: Request Access (Workflow)
Requirement Reference: User access request workflow
Preconditions: Guest user on registration/request page
Test Steps:
1. Fill out the "Request Access" form
2. Admin logs in and navigates to "Access Requests"
3. Admin clicks "Approve"
Expected Result:
User is notified of approval and can now login to the system.
Status: Pass

---

Test Case ID: TC-07
Test Name: Suspend User (Dated)
Requirement Reference: User Management (Suspension)
Preconditions: Active user exists; logged in as Admin
Test Steps:
1. Navigate to Users list
2. Select a user and click "Suspend"
3. Choose "Dated Suspension" and select Start/End dates
4. Save
Expected Result:
User's status changes to "Suspended" and they are unable to login during the specified date range.
Status: Pass

---

Test Case ID: TC-08
Test Name: Send Internal Email
Requirement Reference: Internal Email System
Preconditions: Logged in as Admin
Test Steps:
1. Navigate to a user's profile or "Send Email" page
2. Compose a message and click "Send"
3. Check the "Email History" for that user
Expected Result:
Email is logged in the `email_logs` table and visible in the admin history view.
Status: Pass

---

Test Case ID: TC-09
Test Name: View Audit Trail
Requirement Reference: Activity Logging
Preconditions: Actions (like user creation/edit) have been performed
Test Steps:
1. Navigate to "Activity Log" (admin/activity-logs)
2. Review the list of recent actions
3. Click "Details" on an entry
Expected Result:
System shows the user who performed the action, the timestamp, and before/after data snapshots.
Status: Pass

---

Test Case ID: TC-10
Test Name: Generate Expired Passwords Report
Requirement Reference: Administrative Reports
Preconditions: At least one user has a password older than 90 days
Test Steps:
1. Navigate to Reports -> Expired Passwords
2. Review the generated list
Expected Result:
The report correctly identifies users with expired passwords and shows the number of days overdue.
Status: Pass
