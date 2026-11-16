# Counter POS · Tasks for Today

IT0049 Technical Summative Assessment 4. This extends the supplied Counter POS
project with Eloquent task CRUD, Breeze authentication, soft archiving, and
automatic completion emails. The original visual style and store directories
are retained.

## Run locally

Requirements: PHP 8.2+, Composer 2, and PHP extensions ctype, curl, dom, fileinfo,
filter, hash, mbstring, openssl, pcre, pdo, pdo_sqlite, session, tokenizer, xml,
and xmlwriter. Laravel 12 is retained for compatibility with PHP 8.2; the
activity references Laravel 13 documentation but does not specify an upgrade.

Extract the ZIP and open a terminal inside **pos-foundations**, the folder with
**artisan** and **composer.json**:

    composer install
    php scripts/setup.php
    composer check
    php artisan serve

Open **http://localhost:8000**. Keep the terminal running; Ctrl+C stops the
server. Setup creates an environment file, application key, SQLite database,
and seeded records. Existing environment settings and database contents are
preserved. No Node, NPM, Vite build, queue worker, or real email inbox is needed
with the default log-mail configuration.

Choose **Create account** to register your own login. Seeded team accounts have
random, unpublished passwords; there is no shared default password. All
signed-in users manage the shared task list. Email verification is supported
but is not required to manage tasks.

For XAMPP, run **php --ini** and enable **extension=pdo_sqlite** and
**extension=sqlite3** in the active php.ini. Resolve any missing PHP extensions
reported by Composer before continuing. A missing vendor/autoload.php means
Composer installation has not completed.

## Pages and access

| Page / action | URL | Access |
| --- | --- | --- |
| Welcome and task counts | / | Public |
| Task list, search, filters, pagination | /tasks | Public |
| Active task details | /tasks/{task} | Public |
| Student/project profile | /profile | Public |
| About | /about | Public |
| Create task | GET /tasks/create, POST /tasks | Logged in |
| Edit/update task | GET /tasks/{task}/edit, PUT/PATCH /tasks/{task} | Logged in |
| Archive task | DELETE /tasks/{task} | Logged in |
| Personal account settings | /account | Logged in |
| Store account directories | /customers, /users | Logged in |
| Authentication | /register, /login, /forgot-password | Guest |

Authentication middleware protects write endpoints themselves. Forms use CSRF
tokens and method spoofing. Blade escapes user text. Registration and account
updates cannot assign roles or change account status.

The public Profile describes the student/project. Editing your own login is a
separate Account Settings page. The existing account directories now query
Eloquent and require login, keeping registered user emails out of public pages.

## Database and validation

Migrations and seeders are included, satisfying the database submission
requirement without an export. Setup seeds five customers, five fictional team
accounts, and five tasks. Task fixture IDs are 3001–3005. Re-seeding skips
existing task fixtures, including renamed/archived ones, and preserves existing
sample-account password hashes.

| Task field | Rules / behavior |
| --- | --- |
| title | Required trimmed string, maximum 255 characters, single line |
| description | Optional string, maximum 5,000 characters |
| due_date | Required real calendar date in YYYY-MM-DD format |
| priority | Required: low, medium, or high |
| status | Required: pending, in_progress, or done |
| is_archived | Server-controlled boolean, initially false |
| created_by | Authenticated creator ID, set by server code |
| completed_at | Set on completion, retained on done-to-done edits, cleared on reopening |

Past dates are allowed for overdue work. Blank titles, malformed dates, unknown
status/priority values, oversized input, and array values in string fields are
rejected. Validation errors and old input appear on the form. Archiving sets
**is_archived = true** and retains the row. Archived tasks are excluded from
public lists/counts and return 404 on details, edit, update, and repeat archive
requests.

The attached TSA4 sheet is the requirements source. It refers to TSA3/TFA7,
which were not attached. The rules above are the adopted field specification;
compare them with any separately supplied TFA7 requirements before submitting.

SQLite is the default. To use MySQL, create an empty database, enable pdo_mysql,
and configure the following environment variables:

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=counter_tasks
    DB_USERNAME=your_database_user
    DB_PASSWORD=your_database_password

Then run **php artisan config:clear** and **php artisan migrate --seed**.
For an existing installation of the attached project, back up the database,
install the updated source/dependencies, and run migrate --seed. Do not run
migrate:fresh on data you want to keep.

## Laravel Breeze

The Composer development dependencies declare **laravel/breeze**. The Blade
authentication scaffold is included and adapted to the existing CSS, required
username/role/status columns, and public Profile route. Source is in
**routes/auth.php**, **app/Http/Controllers/Auth/**, and
**app/Http/Requests/Auth/LoginRequest.php**.

Included flows: registration, login/logout, remembered login, rate limiting,
password reset, signed email verification, password confirmation, and account
and password changes. Login accepts active accounts and normalizes email case.
Registration assigns Staff and Active on the server.

**composer install** installs Breeze and the other declared dependencies.
The scaffold is already included; running **php artisan breeze:install** here
would overwrite customized files and is unnecessary. Breeze is a scaffolding
dependency; the generated controllers work with production --no-dev installs.

## Configure and verify email

The default environment uses Laravel's safe **log** mail transport:

    MAIL_MAILER=log
    MAIL_LOG_CHANNEL=single
    MAIL_FROM_ADDRESS=hello@example.test
    MAIL_FROM_NAME="Counter POS Tasks"
    TASK_NOTIFICATION_EMAIL=tasks@example.test

Set TASK_NOTIFICATION_EMAIL to the configured recipient to display in the
generated message. Run **php artisan config:clear** after environment changes.
Messages are written to **storage/logs/laravel.log**, with no actual inbox
delivery. Verification and password reset messages also use this transport.

Demonstrate the required flow:

1. Register/log in and create a Pending task with a unique ASCII title, such as
   **TSA4 email proof**.
2. Edit it, select Done, and save. Confirm the completion timestamp and one
   **Subject: Task completed: TSA4 email proof** header in the log.
3. Edit its description while leaving it Done. Save again. The same subject
   header must still occur exactly once.
4. Reopen it as Pending, then mark it Done again. This is a new transition and
   generates another completion email.
5. Archive the task. Confirm it disappears from the active list and its
   retained database row has is_archived = 1.

PowerShell subject-count command for steps 2 and 3:

    (Select-String -Path .\storage\logs\laravel.log -SimpleMatch "Subject: Task completed: TSA4 email proof").Count

Expect **1** after both saves. Use a fresh title for another demonstration so
old messages do not affect the count.

**TaskController::update()** compares the persisted pre-update status with the
validated new status and calls **Mail::to(...)->send(new TaskCompleted(...))**
only on an unfinished-to-done transition. The Mailable uses HTML and plain text
Blade views. Creating a task initially marked Done does not trigger this update
notification. Seeders do not send notifications.

Updates re-read the stored task inside a database transaction with a row lock.
A transport failure rolls back the task and shows a retry message. The default
log flow is synchronous. External SMTP acceptance followed by a failed database
commit is outside this simple flow's exactly-once guarantees; an outbox would be
needed for stronger production delivery guarantees.

For a local Mailpit instance or safe test SMTP inbox, switch to its settings:

    MAIL_MAILER=smtp
    MAIL_SCHEME=
    MAIL_HOST=127.0.0.1
    MAIL_PORT=1025
    MAIL_USERNAME=
    MAIL_PASSWORD=

Use your sandbox's host, port, scheme, and credentials. Keep credentials in
the environment file and out of Git.

## Checks and tests

    composer check

This lists routes, compiles Blade views, runs native PHP lint on source and
compiled views, and runs PHPUnit. **composer test** runs only the feature suite.
Tests force an isolated in-memory SQLite database and use fake mail and
notifications, except one real log-transport test that removes its dedicated
test log afterward.

Coverage includes public/protected routes, CRUD, validation, escaping, filters,
pagination, archiving, seeders, authentication, password reset/verification,
account updates, completion emails, duplicate prevention, and mail-failure
rollback. GitHub Actions runs the checks with PHP 8.2 and 8.4 after you push.

See **VERIFICATION.md** for checks actually completed during preparation.
PHP/Composer were unavailable here, so no successful Laravel/PHPUnit run is
claimed. The first **composer install** resolves dependencies and generates
composer.lock; commit it after checks pass so hosting uses the same versions.

## Host and submit

Use a PHP-capable host with PHP 8.2+, Composer, and persistent SQLite or MySQL.
Static-only hosting such as GitHub Pages cannot run Laravel.

1. Push the raw project source to your GitHub repository. Include migrations,
   seeders, tests, and the generated composer.lock. The supplied .gitignore
   excludes environment files, dependencies, local databases, logs, and sessions.
2. Deploy the same tested commit. Install production dependencies with
   **composer install --no-dev --optimize-autoloader**.
3. Set APP_ENV=production, APP_DEBUG=false, your HTTPS APP_URL,
   SESSION_SECURE_COOKIE=true, database settings, and mail settings. For SQLite,
   use an absolute DB_DATABASE path on persistent storage.
4. Generate APP_KEY once for a new installation with **php artisan key:generate**;
   preserve an existing production key. Run **php artisan migrate --force**.
   Optionally run **php artisan db:seed --force** for fictional demo records.
5. Set the web server's document root to **public/**. Make storage/ and
   bootstrap/cache/ writable by the PHP process. Apache rewrite rules are
   included in public/.htaccess.
6. Run **php artisan optimize**. Verify the four public pages, authentication,
   CRUD, archiving, and the two-save email proof on the host.
7. Add the real repository and application URLs to **SUBMISSION_CHECKLIST.md**,
   then check both in a private browser window.

No GitHub repository or live deployment was created by editing this ZIP.
Those external submission steps remain to be completed in your accounts.

## Source map

| Responsibility | Location |
| --- | --- |
| Page and management routes | routes/web.php, routes/auth.php |
| Task CRUD and email trigger | app/Http/Controllers/TaskController.php |
| Shared validation | app/Http/Requests/TaskRequest.php |
| Task and active scope | app/Models/Task.php |
| Mailable and email views | app/Mail/TaskCompleted.php, resources/views/emails/ |
| Public pages and task forms | resources/views/pages/, resources/views/tasks/ |
| Schema and demo records | database/migrations/, database/seeders/ |
| Local/CI checks | scripts/check.php, tests/Feature/, .github/workflows/tests.yml |

## References

- [Laravel 12 documentation](https://laravel.com/docs/12.x)
- [Laravel Mail](https://laravel.com/docs/12.x/mail)
- [Laravel Breeze source and license](https://github.com/laravel/breeze)
- [Laravel deployment](https://laravel.com/docs/12.x/deployment)

The assessment references [Laravel 13 Mail](https://laravel.com/docs/13.x/mail)
and [Laravel 13 Starter Kits](https://laravel.com/docs/13.x/starter-kits).
