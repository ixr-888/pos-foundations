# TSA4 submission checklist

Student: **Nazrin Ivan Bardinas**  
Course: **IT0049 · Web System Technologies**  
Section: **[complete]**  
Professor: **[complete]**

## Required links

- GitHub repository: **[paste the real repository URL]**
- Hosted working Laravel application: **[paste the real HTTPS application URL]**
- Submitted commit: **[paste the Git commit SHA]**

These are placeholders. No repository or deployment was published while
preparing this ZIP.

## Before submission

- [ ] Install dependencies and run php scripts/setup.php.
- [ ] Run composer check successfully and retain the output.
- [ ] Commit the generated composer.lock and raw source files.
- [ ] Open Welcome, Task List, Profile, and About as a guest.
- [ ] Confirm guests cannot create/edit/update/archive, including direct requests.
- [ ] Register/log in; create, edit, and archive a task.
- [ ] Demonstrate errors for a blank title, bad date, and invalid status.
- [ ] Show one Pending → Done email and no repeat on Done → Done.
- [ ] Show the retained archived row with is_archived = 1.
- [ ] Compare field rules with any separately supplied TFA7 sheet.
- [ ] Deploy the same tested commit to a PHP-capable host.
- [ ] Check both required links in a private browser window.
- [ ] Fill in section/professor details and the real links above.

## Rubric traceability

| Criterion | Supplied implementation / evidence to collect |
| --- | --- |
| Functionality (30) | Four public pages; active task details; validated create/update; boolean soft archive; migrations/seeders. Demonstrate the running app. |
| MVC (20) | Controllers, Form Requests, Eloquent models, Mailable, and Blade views are separated. |
| Validation/security (25) | Breeze scaffold; auth on management routes; CSRF; escaped output; unique account fields; login throttling. Run the tests. |
| Email (15) | Mail facade sends only on a transition to Done. Capture the unique subject count after first and second saves. |
| Documentation/submission (10) | Setup/mail/deployment README and organized source supplied. Add actual GitHub and live-host URLs. |

## Suggested demonstration captures

1. Public task list in a guest browser.
2. Guest management URL redirecting to login.
3. Completed form and validation-error example.
4. Pending → Done and the generated message in the log or safe SMTP inbox.
5. Done → Done save with subject count still one.
6. Archived task absent from the list and its retained database row.
7. Successful composer check output and the deployed application URL.

Capture these from the real running application. This ZIP does not include
fabricated runtime, email, or deployment evidence.
