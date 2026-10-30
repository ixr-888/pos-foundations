# Counter POS — TFA6: From Arrays to Eloquent

A four-page Laravel POS account directory for IT0049 TFA6. Customer and staff
pages now retrieve database records through Eloquent, and Blade renders model
properties such as `full_name`. The overview also queries the database for its
counts and recently joined customers. Search, filters, CSV exports, styling,
and navigation are retained from the uploaded TFA5 project.

## Run locally

Requirements: PHP 8.2+, Composer 2, and PHP's `pdo_sqlite`, `sqlite3`, `mbstring`,
`curl`, `dom`, `xml`, and `xmlwriter` extensions in addition to Laravel's standard
extensions. Laravel 12 is retained for compatibility with PHP 8.2 installations.
The activity sheet links to Laravel 13 documentation but does not require a
particular framework version.

Extract the ZIP, open a terminal inside **pos-foundations** (the folder with
`artisan` and `composer.json`), then run these commands one at a time:

```powershell
composer install
php scripts/setup.php
composer test
php artisan serve
```

Open **http://localhost:8000**. Keep the server terminal open. SQLite is the
default; XAMPP's MySQL server is not needed for this configuration. CSS and JS
are served directly from `public/`; there is no npm build step.

The setup script creates `.env` and the SQLite file when absent, generates an
application key when needed, and runs migrations and seeders. The two seeders
provide five fictional customers and five staff accounts. Repeated seeding
updates the same sample IDs without adding duplicate rows or resetting staff
password hashes. Seeding deliberately restores those five demo records; do not
use it to preserve edits to demo records.

If PHP or Composer is not recognized, install/configure it and restart your
terminal. For XAMPP, run `php --ini`, enable the required extensions in that
PHP configuration, then retry. If Composer fails, resolve that failure before
running Artisan: Composer creates `vendor/autoload.php`.

## Upgrade an already-created TFA5 database

Keep your existing `.env` and database file, copy the revised project source,
and run:

```text
composer install
php artisan config:clear
php artisan migrate
```

The two original table-creation migrations are retained as migration history.
The third migration, `2026_10_30_000003_rename_account_names_to_full_name.php`,
renames `name` to `full_name` in both tables. It preserves rows and can reverse
the rename through its `down()` method. Fresh installations run all three
migrations in order and reach the same final schema.

Do not use `migrate:fresh` for an existing database: it drops its tables.
Run `php artisan db:seed` separately only when you want to restore the supplied
sample accounts. Run application tests in the included isolated testing
configuration; the PHPUnit configuration uses an in-memory SQLite database.

## Required schema and retained display fields

| Table | Required TFA6 fields in the final schema | Retained fields for the original pages |
| --- | --- | --- |
| customers | `full_name` string, `email` string (unique), `phone` string | `id`, `city`, `status`, `joined_on`, timestamps |
| users | `username` string (unique), `full_name` string | `id`, unique `email`, `role`, `status`, password hash, email verification, remember token, timestamps |

The explicitly named TFA6 fields are present. Extra fields are retained so the
existing customer and team directories keep their original columns and filters.
TFA2 itself was not attached, so this is based on the schema stated in the TFA6
sheet; compare it with your TFA2 work if that sheet includes further constraints.
Phone numbers remain strings to preserve prefixes and formatting. Usernames
are unique at the database level. Account IDs stay numeric: `C-` and `U-` are
presentation prefixes added only in Blade.

Staff passwords are hashed and not displayed. The seeder generates random
passwords for new staff accounts; this read-only activity has no login screen.
Role labels do not implement authorization.

## MVC mapping

| URL | Controller | Database access | Blade view |
| --- | --- | --- | --- |
| `/` | `PageController::home` | Model counts and three most recent customers | `pages/home.blade.php` |
| `/about` | `PageController::about` | No records required | `pages/about.blade.php` |
| `/customers` | `CustomerController::index` | `Customer::query()->orderBy('id')->get()` | `customers/index.blade.php` |
| `/users` | `UserController::index` | `User::query()->orderBy('id')->get()` | `users/index.blade.php` |

The controllers have no sample arrays or array fallback. Each listing passes
an Eloquent collection to Blade. The views loop over models, output escaped
properties, and format the customer's joining date as `Y-m-d`. Seed sample data
lives in `database/seeders/`, independent of the controllers.

## Verify that pages use the database

Run `composer test` after installing dependencies. The 12 tests cover schema,
five records per table, repeatable seeding and password preservation, unique
usernames, reversible name migration, all four pages, Eloquent view data,
database edits appearing in the pages, changed dashboard totals, empty tables,
escaped output, and a missing route.

A GitHub Actions workflow is included at `.github/workflows/tests.yml` to install
dependencies, check PHP syntax, and run the suite when this folder is the root
of your repository. Its presence does not mean a CI run has passed.

For a manual check with SQLite, open `database/database.sqlite` in a SQLite
editor; for MySQL, use phpMyAdmin or another client. Edit one customer's
`full_name`, save, and refresh `/customers`. The edited value should appear.
Change it back afterwards. The PHPUnit suite includes the same database-to-page
check without changing your local database.

Also check the following in the browser:

1. `/`, `/about`, `/customers`, and `/users` all load.
2. The directories initially show five seeded records each.
3. Searching for Ana returns Ana Reyes, and resetting restores the rows.
4. The Inactive customer filter finds Gabriel Lim.
5. Cashier plus Inactive finds Lea Navarro on the staff page.
6. CSV export contains only the visible records and excludes avatar initials.
7. Navigation, empty search results, and mobile layout remain usable.

See `VERIFICATION.md` for checks actually performed and remaining runtime checks.

## AI review materials

- `docs/original-draft/`: unchanged copies of the uploaded migrations, seeders,
  sample-data controllers, README, and historical static preview.
- `docs/MIGRATION-SEEDER-REVIEW.md`: review findings, corrected field/data plan,
  a migration-versus-manual-MySQL explanation, and AI contribution disclosure.
- `docs/SUBMISSION-CHECKLIST.md`: code, evidence, repository, and hosting items.

The historical preview is retained only as original evidence. It does not run
Laravel or demonstrate Eloquent. Use the actual Laravel application for TFA6.
Take genuine screenshots of the complete ChatGPT conversation yourself; no
conversation screenshots are fabricated or included in this ZIP.

The original uploaded README already disclosed AI generation. Its preserved
files are a before-revision snapshot, not evidence of an independently authored
pre-AI draft. If you have your own earlier draft, submit that as the original.
This revision also used AI to edit the application, migration, seeders, tests,
and documentation. Review the changes and disclose that scope accurately.

## Use MySQL instead of SQLite

Create a MySQL database called `pos_foundations`. Set the following values in
`.env`, using your own database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pos_foundations
DB_USERNAME=root
DB_PASSWORD=
```

Remove or update any old `DB_DATABASE` path or `DB_URL` pointing to a different
database. Enable `pdo_mysql`, start MySQL, then run:

```text
php artisan config:clear
php artisan migrate --seed
```

Migrations and seeders satisfy the sheet's database-submission option; a SQL
export is not required in addition. This configuration is provided but has not
been executed against MySQL in this environment.

## GitHub and hosted links required for submission

Code changes alone do not supply the two submission links. Create a GitHub
repository and upload the contents of **pos-foundations** as its repository
root, including hidden files such as `.gitignore` and `.github/`.

If using Git, create an empty repository first, then run these commands from
inside the project, replacing the sample remote with your real repository URL:

```text
git init
git add .
git commit -m "Convert POS directories from arrays to Eloquent"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/pos-foundations.git
git push -u origin main
```

Commit `composer.lock` after the first successful dependency installation.
Keep `.env`, `vendor/`, database files, compiled views, and logs out of Git;
the included `.gitignore` excludes them.

Host on a service supporting PHP 8.2+ and Laravel 12. GitHub Pages cannot run
this PHP application. On the host:

1. Deploy the repository and run `composer install --no-dev --optimize-autoloader`.
2. Set `.env` or server environment values: `APP_ENV=production`, `APP_DEBUG=false`,
   `APP_URL` to the real HTTPS address, and the database credentials.
3. Generate a unique `APP_KEY` using `php artisan key:generate` after creating
   the server `.env`. Keep it private and stable across deployments.
4. Point the document root at `public/`. Make `storage/` and `bootstrap/cache/`
   writable by the web-server process. Enable URL rewriting as required by
   the server.
5. For SQLite, use a persistent, writable database file and set `DB_DATABASE`
   to its absolute server path. For MySQL, provision the database first.
6. Run `php artisan migrate --force`. For a new demonstration database, run
   `php artisan db:seed --force` once, then `php artisan optimize`.
7. Open all four hosted pages and verify database-backed output.

Write your actual repository URL and hosted URL in the submission checklist.
No remote repository, public deployment, or CI run was created by this edit.

## References

- [Laravel migrations](https://laravel.com/docs/12.x/migrations)
- [Laravel seeding](https://laravel.com/docs/12.x/seeding)
- [Laravel Eloquent](https://laravel.com/docs/12.x/eloquent)
- [Blade](https://laravel.com/docs/12.x/blade)
- [Laravel deployment](https://laravel.com/docs/12.x/deployment)
