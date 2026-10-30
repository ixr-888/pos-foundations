# TFA6 submission checklist

## Included source changes

- [x] Customer and staff tables end with `full_name` through a reversible migration.
- [x] Customer email/phone fields and unique staff username are retained.
- [x] Five fictional samples per table are defined in standalone seeders.
- [x] Customer and User models expose `full_name`.
- [x] Listing controllers query Eloquent instead of static arrays.
- [x] Blade views display model properties with the existing styling.
- [x] Dashboard totals and recent customers query the database.
- [x] Original uploaded draft files and the corrected review/plan are preserved.
- [x] AI contribution and migrations-versus-manual-MySQL explanation are included.
- [x] Database/page tests and a GitHub Actions workflow are provided.

Checked source items mean the changes are included, not that PHP tests ran.

## Complete before submission

- [ ] Review the code and confirm it matches your TFA2 schema and class instructions.
- [ ] Run `composer install` successfully.
- [ ] Run `php scripts/setup.php` for a new database, or `php artisan migrate` for an existing one.
- [ ] Run `composer test` and retain its actual result.
- [ ] Confirm at least five customer and five staff records exist in the database.
- [ ] Confirm database edits appear in Blade pages after refreshing.
- [ ] Check all four pages, search, filters, exports, and mobile layout.
- [ ] Commit the generated `composer.lock` after a successful install.
- [ ] Submit your actual original draft; the preserved upload already disclosed AI assistance.
- [ ] Capture screenshots of the complete ChatGPT conversation.
- [ ] Review/adapt the corrected plan and short explanation for your submission.
- [ ] Add student/group name, section, members if applicable, and professor to the activity sheet.
- [ ] Publish the raw project files to GitHub (this project folder must be the repository root).
- [ ] Deploy and verify the real Laravel app on PHP-capable hosting.
- [ ] Provide the actual repository and live application links below.

Repository URL: **Not yet published**

Hosted application URL: **Not yet deployed**

These are status labels, not submission URLs. Replace them with real working
links before submitting. The historical HTML preview and localhost URL do not
satisfy the public hosted-application requirement.
