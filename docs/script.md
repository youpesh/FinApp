# Sprint 1 Demo Script

*Read the text outside brackets. Text inside [brackets] = action to perform.*

> **Demo Credentials**
> | Role | Username | Password |
> |------|----------|----------|
> | Admin | `admin0126` | `Admin123!` |
> | Manager | `msmith0126` | `Manager123!` |
> | Accountant | `ajones0126` | `Account123!` |

---

### Part 1 — Landing Page & Access Request
*"Welcome to Smart Finance. We're on the public landing page. Unapproved users can't just sign up — they must request access."*

[Action: Show the landing page at the root URL]

*"Clicking 'Request Access' opens a form that collects the user's full name, address, date of birth, and has them set a security question and answer — which we'll use later in the forgot password flow."*

[Action: Click 'Request Access' / 'Create New User', fill out the form with: first name, last name, address, DOB, security question, then submit]

---

### Part 2 — Admin Receives Email & Approves
*"The moment the request is submitted, the administrator receives an email notification with the applicant's full details."*

[Action: Open the Mailpit inbox at `http://server.taildf970d.ts.net:8025` — show the access request email]

*"The admin also sees the request in the Access Requests queue. They can review it and choose to approve or reject."*

[Action: Log in as `admin0126 / Admin123!`, navigate to Access Requests, approve the pending request]

*"On approval, the system automatically generates a username using the first initial, full last name, and the two-digit month and year of account creation. This standardized format ensures every user has a unique, consistent identifier."*

[Action: Show the success message displaying the generated username]

*"The new user is immediately emailed their username and a temporary password."*

[Action: Switch to Mailpit and show the welcome email with the username and credentials]

---

### Part 3 — First Login & Forced Password Change
*"The user logs in with the username from the email."*

[Action: Log out, then log in with the new user's username and temporary password]

*"Because this is a generated temporary password, the system immediately requires the user to set a new password before they can access anything."*

[Action: Show the forced password change screen that appears immediately after login]

*"The new password must be at least 8 characters, start with a letter, and include a number and special character. The system also prevents reusing any previous password."*

[Action: Try a weak password to show the error, then try a previously used one to show the history check, then set a valid new password]

---

### Part 4 — Personalized Dashboard & Role-Based Access
*"Once logged in, the user's name and profile picture are displayed persistently in the sidebar — a clear indicator of who is in the session."*

[Action: Point to the top-right of the sidebar showing name and avatar]

*"Each role sees a different view. Managers see the Management section with journal entry controls, while Accountants see their Accounting section. Administrators see all admin controls."*

[Action: Log in as `msmith0126` (Manager) to show the Management section, then as `ajones0126` (Accountant) to show the Accounting section]

---

### Part 5 — Forgot Password (Email + Username + Security Question)
*"If a user forgets their credentials, the password recovery flow requires both their email address AND their username — not just one. This dual verification is a deliberate security measure."*

[Action: Click 'Forgot your password?' on the login page, enter the email and username]

*"After identity is verified, they're asked to answer their security question that they set during registration."*

[Action: Show the security question prompt and answer it]

*"Once answered, they can set a new password — again enforcing complexity rules and password history."*

[Action: Set a new valid password on the reset screen]

---

### Part 6 — Automated Security: Failed Login Lockout
*"The platform protects against brute force attacks. Three consecutive failed login attempts automatically suspend the account."*

[Action: Log out, attempt login with wrong password 3 times — show the suspension message on the 3rd attempt]

*"The account is then in a 'Suspended' state, which only an administrator can reverse."*

---

### Part 7 — Admin Management & Reporting
*"Back as administrator, we have full oversight of the user base. The Users page lets us filter by role or status, edit any user's details, or send them a direct email from within the system."*

[Action: Log in as `admin0126`, navigate to Users, show the filter controls, click Edit on a user, then click Email]

*"We can also suspend a user for a defined date range — useful for extended leave — by setting a start and end date on their account."*

[Action: Show the suspension date range fields in the Edit User form]

*"The Reports section gives the administrator two key reports required by the system: a full user roster..."*

[Action: Navigate to Reports → Users — show the full list]

*"...and a report of all users with expired passwords, showing exactly how many days overdue each one is."*

[Action: Navigate to Reports → Expired Passwords]

*"Finally, the Activity Log records every significant action taken in the system — logins, user creation, approvals, status changes — giving us a complete audit trail."*

[Action: Navigate to Activity Log and show some recent entries]

---

### Part 8 — Password Expiry Notifications
*"Passwords expire after 90 days. Three days before expiry, the system automatically sends the user an email reminder. The administrator also has a report to monitor this."*

[Action: Show the Expired Passwords report — point out any users with expired or near-expired passwords]
[If needed: Show the Mailpit inbox to demonstrate what the warning email looks like]

---

*"That concludes our Sprint 1 demonstration. Smart Finance now has a fully secure, role-based user management foundation — complete with automated notifications, audit trails, and robust access controls — ready for our financial reporting modules in Sprint 2. Thank you."*