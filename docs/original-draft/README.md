# Counter POS Foundations

A four-page Laravel POS foundation for IT0049 Technical Formative Assessment 5.
The landing page summarizes the sample accounts. Customer Accounts and User
Accounts display five fictional records each from static PHP arrays in their
respective controllers.

## View the output immediately

Open **preview.html** in a browser. Its navigation, search, filters, and CSV
downloads work without installing anything. This is an offline visual preview
generated from the Blade markup and the controllers' sample records; it does
not run Laravel or replace the hosted Laravel application required for submission.
The preview is a packaged snapshot; editing PHP files does not update it.

## Run the Laravel application

Requirements: PHP 8.2 or newer, Composer 2, and the PHP extensions listed in
[Laravel's server requirements](https://laravel.com/docs/12.x/deployment#server-requirements).
Enable PDO SQLite as well for the included database setup.

Extract this project, open its folder in a terminal, and run:

```powershell
composer install
php scripts/setup.php
php artisan serve
```

Open **http://localhost:8000** and keep the terminal running. Stop the server
with Ctrl+C. The setup script creates a local .env, generates an application
key, creates the SQLite file, and runs migrations and seeders. It preserves
existing environment settings and can be run again.

The CSS and JavaScript are served directly from public/. No Node, NPM, Vite
build, authentication package, or external design library is needed.

If PHP or Composer is not recognized, install them first using the
[official Laravel installation instructions](https://laravel.com/docs/12.x/installation),
then restart your terminal. This copy targets Laravel 12 and supports PHP 8.2,
including XAMPP installations reporting PHP 8.2.4.

If Composer fails, stop before running the setup or server commands. The
vendor/autoload.php file is created during a successful Composer installation;
its absence after an installation failure is a follow-on error.

For XAMPP, use php --ini to locate the active php.ini. If the setup script reports
a missing SQLite extension, enable extension=pdo_sqlite and extension=sqlite3 in
that file, save it, and run the setup script again.

## Required pages and MVC mapping

| URL | Controller method | Blade view | What it displays |
| --- | --- | --- | --- |
| / | PageController::home | resources/views/pages/home.blade.php | Landing page and counts derived from sample records |
| /about | PageController::about | resources/views/pages/about.blade.php | Application purpose, features, and project information |
| /customers | CustomerController::index | resources/views/customers/index.blade.php | Five customers from a static PHP array |
| /users | UserController::index | resources/views/users/index.blade.php | Five users from a static PHP array |

All four routes are declared in routes/web.php. Every page extends the shared
resources/views/layouts/app.blade.php layout, which provides navigation to all
four pages. Both account views use **@foreach**.

The static samples are defined in **CustomerController::sampleRecords()** and
**UserController::sampleRecords()**. Each index method passes that controller's
array to its Blade view. Edit those arrays to change the displayed records.

PageController derives the overview totals from the same arrays, so the
summary and directories agree. All names, phone numbers, and addresses are
fictional; the email addresses use the reserved example.test domain.

## Included functionality

- Landing page with customer count, user count, and active user count.
- About page with links to both account directories.
- Customer table with ID, name, email, phone, city, status, and join date.
- User table with ID, name, email, username, role, and status.
- Search, status filters, a user-role filter, and reset controls.
- CSV export of the currently visible records, with decorative avatar letters excluded.
- Empty-result states and live visible-record counts.
- Responsive layouts, labeled inputs, keyboard focus indicators, and a skip link.

This activity is a read-only foundation. Login, role enforcement, record editing,
checkout, payment handling, and sales processing are outside its scope.

## Database submission files

The activity specifies static arrays for the listings but separately requests
a database export or migration and seeder files. This project supplies:

- database/migrations/2026_10_24_000001_create_users_table.php
- database/migrations/2026_10_24_000002_create_customers_table.php
- database/seeders/DatabaseSeeder.php
- database/seeders/CustomerSeeder.php
- database/seeders/UserSeeder.php
- app/Models/Customer.php and app/Models/User.php for the database schema.

The seeders reuse the controller samples so their records match the directories.
Running them again does not duplicate records or replace existing password hashes.
User passwords are randomly generated; no login feature is included.

The listing controllers intentionally **continue to use static arrays**, even
after seeding. Editing the database will not change these exercise pages.
Do not replace those arrays with model queries for this submission unless your
professor changes the requirement.

SQLite is the default and needs no separate database server. If your class
requires MySQL, configure DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE,
DB_USERNAME, and DB_PASSWORD in .env, then run:

```text
php artisan config:clear
php artisan migrate --seed
```

Enable pdo_mysql when using MySQL.

## Verify the Laravel output

With dependencies installed:

```text
php artisan route:list --except-vendor
composer test
```

The PHPUnit suite checks the four routes, shared navigation, five rendered
records on each directory, consistent overview counts, 404 handling, matching
database seeds, and repeatable seeding.

Manually verify that:

1. All four navigation links open the matching page.
2. Customer Accounts and User Accounts initially display five rows each.
3. Searching for "Ana" finds Ana Reyes.
4. An unmatched search displays an empty-result message and zero visible rows.
5. The Inactive customer filter displays Gabriel Lim.
6. The Cashier user filter displays Daniel Torres and Lea Navarro.
7. Cashier plus Inactive displays only Lea Navarro.
8. Reset restores all five rows.
9. Export CSV downloads the rows currently shown.
10. The pages remain usable at a mobile screen width.

Build-time verification and the runtime limitation are recorded in
VERIFICATION.md. PHP, Composer, and the Laravel PHPUnit suite could not be
executed in the creation environment. Run the commands above on your machine
before submission.

## GitHub submission

Create a GitHub repository, then run these commands inside this project folder.
Replace YOUR_USERNAME with your GitHub username and ensure the named repository
already exists.

```text
git init
git add .
git commit -m "Build four-page Laravel POS foundations app"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/pos-foundations.git
git push -u origin main
```

Commit composer.lock after the first successful Composer installation so future
installs use the same dependency versions. Keep .env, vendor/, local databases,
cache files, and logs out of Git. The included .gitignore handles those files.

Before submitting, add your actual repository URL and hosted application URL
to your copy of this README. No repository or live hosting account is created
by this download.

## Host the working application

Use hosting that supports PHP 8.2+ and Laravel 12. GitHub Pages cannot execute PHP.

1. Upload or clone the project on the server.
2. Run composer install --no-dev --optimize-autoloader.
3. Configure .env or server environment variables. Set APP_ENV=production,
   APP_DEBUG=false, and APP_URL to your actual HTTPS address.
4. Generate a unique APP_KEY with php artisan key:generate after creating the
   server .env. If using environment variables instead, generate one with
   php artisan key:generate --show and configure the returned value privately.
5. Make storage/ and bootstrap/cache/ writable by the web server.
6. Point the web server's document root to this project's **public/** directory.
7. If using SQLite, create a writable database/database.sqlite file. Otherwise,
   configure an existing database and its driver.
8. Run php artisan migrate --seed --force, then php artisan optimize.
9. Open the hosted /, /about, /customers, and /users URLs and verify all work.

For Apache, enable mod_rewrite and AllowOverride All for public/ so the included
.htaccess can route requests. Use the hosting provider's PHP process manager
or web server for the public deployment; Artisan's server is for local work.

## Understanding the MVC flow

A request for /customers matches a route in routes/web.php. The route calls
CustomerController::index(). The method obtains the static sample array and
passes it to the customers.index view. Blade's @foreach creates one table row
for each array record, and the layout adds the shared navigation and assets.

The same flow handles /users through UserController and the users.index view.
No database query is needed to render either account page.

## AI assistance disclosure

The initial project code, styling, sample records, tests, and instructions were
generated with AI assistance. Review the source, run it, adapt it to your
original TFA1 design if needed, and disclose AI use according to your class
instructions. Do not claim to have run checks or deployed an application until
you have done so.

## References

- [Laravel installation](https://laravel.com/docs/12.x/installation)
- [Laravel routing](https://laravel.com/docs/12.x/routing)
- [Laravel controllers](https://laravel.com/docs/12.x/controllers)
- [Blade templates](https://laravel.com/docs/12.x/blade)
- [Database seeding](https://laravel.com/docs/12.x/seeding)
- [Laravel deployment](https://laravel.com/docs/12.x/deployment)
