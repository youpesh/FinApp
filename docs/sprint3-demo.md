# Sprint 3 Demo Script — Journalizing & General Ledger

*Read the text outside brackets. Text inside [brackets] = action to perform.*

### Part 1 — Creating a Journal Entry

*"In Sprint 3 we added journalizing — the core of double-entry accounting. Let's create a journal entry."*

[Action: As the Accountant, navigate to Journal Entries, click **Create New Entry**]

*"The form asks for a date, a description explaining the business reason, and optional source documents like receipts or invoices."*

[Action: Set today's date, type a description such as "Purchased office supplies from Staples", attach a sample PDF]

*"Below that we build the transaction lines. Each line picks an account from our Chart of Accounts, a type — debit or credit — and an amount. We can also add a memo to any line."*

[Action: Add a debit line — select "Office Supplies" account, amount $250.00. Add a credit line — select "Cash" account, amount $250.00]

*"Notice the running totals at the bottom. The system enforces that debits equal credits in real time — the submit button stays disabled until the entry is balanced."*

[Action: Point out the Dr/Cr totals and the green "Balanced" indicator]

*"It also enforces proper sequencing: all debit lines must come before credit lines, matching standard accounting convention."*

[Action: Briefly show adding a debit after a credit to trigger the sequence warning, then undo it]

*"Once everything checks out, we submit for approval."*

[Action: Click **Submit for Approval** — show the success message and redirect to the journal list]

---

### Part 2 — Journal Entry List & Filtering

*"The journal entries page shows all entries with their reference ID, date, status, creator, and total debits."*

[Action: Point out the table columns and the amber "Pending" badge on the new entry]

*"We can filter by status, date range, or search by keyword — useful when you have hundreds of entries."*

[Action: Select "Pending" from the status filter, click **Filter** to narrow the list]

---

### Part 3 — Manager Approval Workflow

*"Journal entries require manager approval before they hit the ledger."*

[Action: Switch to the Manager account, navigate to Journal Entries, click **View** on the pending entry]

*"The detail page shows the full transaction — date, creator, description, every debit and credit line with account names, and any attached source documents on the right."*

[Action: Point out the entry lines table, totals, and the source documents sidebar with the attached file]

*"The manager has two options: approve and post to the ledger, or reject with a written reason. If rejected, the accountant sees exactly why. Let's approve this one."*

[Action: Click **Approve & Post to Ledger** — show the success message, the green "Approved" badge, and the "View in General Ledger" link]

---

### Part 4 — General Ledger Overview

*"The General Ledger page gives us a bird's-eye view of every account that has activity."*

[Action: Navigate to **General Ledger** in the sidebar]

*"Each card shows the account number, name, category, transaction count, and computed balance. The cards are clickable."*

[Action: Point out a few account cards — note the balance and transaction count on each]

---

### Part 5 — Ledger Detail & Running Balance

*"Let's drill into the Cash account to see its full history."*

[Action: Click the **Cash** account card]

*"This is the classic T-account ledger view. Every approved transaction is listed with its date, description, post reference linking back to the journal entry, and the debit or credit amount. The running balance updates with each row."*

[Action: Point out the table columns — especially the Post Ref links and the running balance column]

*"We can filter by date range or specific amount to find particular transactions."*

[Action: Demonstrate the date filter or amount filter, then click **Clear** to reset]

*"Clicking a post reference takes us right back to the original journal entry — so there's always a clear audit trail from ledger to source."*

[Action: Click a Post Ref link (e.g., JE-2026-0001) to show it links back to the journal entry detail]

---

*"That wraps up Sprint 3. We now have a complete journalizing workflow with real-time validation, manager approval controls, and a full General Ledger with running balances and audit traceability. Thank you."*
