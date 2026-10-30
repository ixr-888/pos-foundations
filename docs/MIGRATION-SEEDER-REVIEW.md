# Migration and seeder review — TFA6

## Original draft

The unchanged input files are preserved under `docs/original-draft/`:
the two creation migrations, all three seeders, and the customer and user
controllers containing the original sample records. The original README is
also preserved and already discloses AI assistance. This snapshot records the
uploaded project's state before this revision; use your actual own earlier
draft if the activity asks for work you wrote before consulting ChatGPT.

The customer's original columns were `id`, `name`, unique `email`, `phone`,
`city`, `status`, `joined_on`, `created_at`, and `updated_at`. The original staff
columns were `id`, `name`, unique `username`, unique `email`,
`email_verified_at`, `role`, `status`, `password`, `remember_token`, and timestamps.
The sample arrays contained five customers and five staff members.

## 1. Missing fields

The original project used `name` in both tables, while the TFA6 sheet explicitly
requires `full_name`. Rename that column in both tables and update models,
seed data, and Blade references. The customer fields `email` and `phone`, and
the staff field `username`, already exist. Staff username uniqueness already
exists at the database level.

## 2. Questionable data types

Keep names, usernames, and email addresses as strings. Keep `phone` as a string
so `+63`, spaces, and leading zeros are retained. Keep `joined_on` as a date.
String role and status fields are sufficient for the existing read-only pages;
if data-entry features are added later, validate allowed values. Customer email
uniqueness is retained from the upload and assumes one email per customer.

## 3. Seeder data problems

Originally the seeders called controller methods for sample data, coupling
database initialization to page controllers. Store those arrays in the seeders
instead. Retain five distinct fictional records per table and unique staff
usernames and emails. Use model-managed timestamps and preserve existing
password hashes when re-seeding. Random hashed staff passwords are suitable
for this listing exercise, which does not include login testing.

The sample IDs are fixed at 1001–1005 for customers and 2001–2005 for staff to
preserve the original directory labels. Re-seeding restores those demo rows;
these IDs are reserved for coursework samples, not arbitrary existing accounts.

## 4. Blade support

The required columns support both directories. The original controllers used
static arrays, so page output did not prove that database retrieval worked.
Use `Customer` and `User` Eloquent queries, pass their collections to Blade,
and render model properties such as `$customer->full_name` and
`$user->username`. Format the joining date explicitly and keep escaped Blade
output. The overview must use database counts and recent-customer queries too.
Empty tables should render normally without falling back to static samples.

## Corrected migration plan

The original creation migrations are kept so previously migrated databases can
upgrade. A third migration renames `name` to `full_name` in both tables without
deleting the records. Running all migrations on an empty database produces
the same final schema. Its rollback reverses the two column renames.

| Table | Field | Type and rule |
| --- | --- | --- |
| customers | id | Numeric primary key |
| customers | full_name | Required string; TFA6 field |
| customers | email | Required unique string; TFA6 field |
| customers | phone | Required string; TFA6 field |
| customers | city | Required string retained for the existing view |
| customers | status | String, default Active |
| customers | joined_on | Date |
| customers | created_at, updated_at | Laravel timestamps |
| users | id | Numeric primary key |
| users | username | Required unique string; TFA6 field |
| users | full_name | Required string; TFA6 field |
| users | email | Required unique string retained from the original project |
| users | role | Required string retained for the existing view |
| users | status | String, default Active |
| users | password | String storing a hash, never displayed |
| users | email_verified_at | Nullable timestamp |
| users | remember_token | Nullable remember-token string |
| users | created_at, updated_at | Laravel timestamps |

The password-reset-token support table from the upload is retained. It is
not a third directory or a substitute for the required customer/staff tables.
TFA2 was not supplied; additional fields above retain the existing interface
while meeting the field names explicitly listed in TFA6.

## Corrected sample-data plan

| Customer ID | full_name | email | phone |
| --- | --- | --- | --- |
| 1001 | Ana Reyes | ana.reyes@example.test | +63 900 000 1001 |
| 1002 | Miguel Santos | miguel.santos@example.test | +63 900 000 1002 |
| 1003 | Sofia Cruz | sofia.cruz@example.test | +63 900 000 1003 |
| 1004 | Gabriel Lim | gabriel.lim@example.test | +63 900 000 1004 |
| 1005 | Isabella Flores | isabella.flores@example.test | +63 900 000 1005 |

| Staff ID | username | full_name |
| --- | --- | --- |
| 2001 | alex.mendoza | Alex Mendoza |
| 2002 | camille.garcia | Camille Garcia |
| 2003 | daniel.torres | Daniel Torres |
| 2004 | lea.navarro | Lea Navarro |
| 2005 | marco.rivera | Marco Rivera |

The corresponding seeders also supply the retained city, status, date, role,
and staff email fields. User passwords are generated and hashed on first
creation; subsequent seeding preserves them.

## Short explanation: migrations versus manual MySQL tables

Laravel migrations define database structure in version-controlled PHP files
that can be shared and applied consistently across environments. Their `up()`
and `down()` methods describe how to apply and reverse schema changes.
Manually creating tables through MySQL or phpMyAdmin changes that database
directly, so the equivalent steps must be recorded and repeated separately.
Seeders serve a different purpose: they populate the tables with initial or
sample records after the structure exists.

## AI contribution and evidence

AI reviewed the uploaded draft and the TFA6 sheet, then edited the application,
added the rename migration, revised seeders, updated Blade views, and prepared
tests and documentation at the user's request. The revision therefore includes
AI implementation assistance in addition to the initial review. Do not describe
it as review-only assistance.

Source checks and JavaScript checks are documented in `VERIFICATION.md`.
PHP, Composer, and browser execution were unavailable in the editing environment;
Laravel runtime tests are provided but must be run before claiming they passed.
Capture genuine screenshots of the complete ChatGPT conversation and add them
to your submission. Review the code and record your own verification results.
