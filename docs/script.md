# Sprint 1 Demo - Natural Narration Script

*Instructions: Read the text outside of the brackets. The text inside [brackets] indicates the action you should perform in the application.*

> **Demo Credentials:**
> | Role | Username | Password |
> |------|----------|----------|
> | Admin | `admin0126` | `Admin123!` |
> | Manager | `msmith0126` | `Manager123!` |
> | Accountant | `ajones0126` | `Account123!` |

---

### [Part 1: Access & Registration]
"Welcome to the first demonstration of our accounting platform, **Smart Finance**. We're starting here on the login page. For new users, we've implemented a self-service registration flow where individuals can request access to the system."

[Action: Click 'Create New User' to show the Request Access form]

"The registration form collects all essential profile data—including contact information and birth date—to help administrators verify each request. Once submitted, this request moves into a pending state, awaiting official approval."

---

### [Part 2: Administrator Approval & Identity]
"Now, switching to the Administrator's perspective, we can see the incoming request queue. When an administrator approves a new user, the system automatically handles the identity creation."

[Action: Log in as admin (`admin0126 / Admin123!`), navigate to Access Requests, and approve a pending user]

"The platform generates a unique username following our consistent internal pattern: we use the first initial, the full last name, and the month and year the account was opened. This ensures that every user has a unique, standardized identifier from day one."

---

### [Part 3: Personalized Experience]
"Now that the account is active, let's sign in with the username that was emailed to the new user. Notice that the interface is designed for clear session management — once logged in, the user's name and profile picture are displayed in the header at all times."

[Action: Log out, then log in with the newly created user's username and temporary password]

"This header remains consistent throughout the app, alongside our company branding, providing a familiar navigation anchor as users move between modules."

---

### [Part 4: Security Logic & Enforcement]
"Security is built into the core of the platform. For example, when setting up or changing a password, we enforce strict complexity rules: a minimum of eight characters, starting with a letter, and requiring a mix of numbers and special symbols."

[Action: Open the password change screen and show the error for a weak password]

"Beyond complexity, we also prevent the reuse of past passwords and ensure all credentials are encrypted. To maintain long-term security, every password expires after 90 days, with the system providing a friendly reminder three days before it's time for an update."

---

### [Part 4b: Password Recovery]
"If a user ever forgets their credentials, they can use the Forgot Password flow. The system requires both the user's email address and their username to verify identity, before asking them to answer the security question they set during registration."

[Action: Click 'Forgot your password?' on the login page, enter email and username, then show the security question prompt]

---

### [Part 5: Automated Safeguards]
"We've also included automated safeguards against unauthorized access. If the system detects three consecutive failed login attempts, the account is automatically suspended to protect the user's data."

[Action: Log out and perform 3 failed login attempts to trigger the suspension message]

---

### [Part 6: Management & Oversight]
"Finally, the platform provides Administrators with the oversight they need. Administrators can access specialized reports to view the entire user base, track expired passwords, or manage personnel changes—such as suspending an account for a user going on extended leave."

[Action: Log in as admin (`admin0126 / Admin123!`), show the User List report and Expired Passwords report]

[Action: Show the date-range suspension tool in Edit User]

"From this same interface, administrators can even communicate directly with users via email, ensuring that user management remains efficient and centralized within Smart Finance."

[Action: Click 'Email' on a user from the Users page]

"That concludes our Sprint 1 showcase. We now have a fully secure, role-based foundation ready for our financial reporting modules. Thank you."