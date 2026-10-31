# gouwwaas.be

Laravel 13 website (PHP 8.4+) for Gouw Waas.

## Setup

1. `composer install`
2. `cp .env.example .env` and `php artisan key:generate`
3. Configure the database in `.env`:
   ```
   DB_CONNECTION=mysql   # or pgsql, sqlite, sqlsrv
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=gouwwaas
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. `php artisan migrate`
5. Create a login: `php artisan user:create "Your Name" you@example.com` (asks for a password)
6. `php artisan serve` and open http://localhost:8000

## Pages

The site is in Dutch (`APP_LOCALE=nl`).

- `/` – home: introduction and how to join (`resources/views/home.blade.php`)
- `/districten` – the four districts with their groups, each linking to its website
  (`resources/views/districten.blade.php`)
- `/wie-is-wie` – gouwbureau, adjuncten and districtscommissarissen
  (`resources/views/wie-is-wie.blade.php`)
- `/activiteiten` – activities (`resources/views/activiteiten.blade.php`)
- `/login` – login; once logged in the header shows "Welkom {naam}!"
- `/wachtwoord-vergeten` – request a password reset e-mail
- `/profiel` – logged-in users: see name and e-mail, change password
- `/gebruikers` – admins only: add, edit and delete users, force a password reset

**Users and admins.** Admins (`users.is_admin`) manage the other users. The flag
cannot be set through the website: the account that existed when the
`add_is_admin_to_users_table` migration ran became the admin. New users get an
e-mail to choose their own password; "Wachtwoord resetten" makes the current
password unusable, logs the user out everywhere and e-mails a reset link. Reset
links are valid for 24 hours.

**Districts and groups** are listed in `config/gouw.php` (source: Scoutnet) as
`[name, municipality, group number, website]`. Edit that file to add, remove or
rename a group or change its website (`null` = no link); the counts update
automatically.

Styling lives in `public/css/app.css` (no build step required). Colours come from
the Gouw Waas emblem (`public/images/logo-*.webp`) and the Scouts en Gidsen
Vlaanderen house style, with Zilla Slab and Nunito Sans (self-hosted in
`public/fonts`, SIL OFL) standing in for the paid house fonts.

## Tests

`php artisan test` (uses an in-memory SQLite database).

## Deployment

Every push to `main` runs the tests in GitHub Actions and, when they pass, deploys
to production with [Deployer](https://deployer.org) (`deploy.php`,
`.github/workflows/deploy.yml`).

- The code and the production `vendor/` folder are built in CI and uploaded over
  SSH with rsync. The server needs PHP and rsync, but not git or composer.
- Each deploy goes to a new folder in `releases/`. The `current` symlink only
  switches when everything succeeded (zero downtime). The last 5 releases are kept.
- `storage/` and `.env` are shared between releases, in `shared/`.
- The production `.env` is generated from GitHub secrets on every deploy.
- Migrations run automatically (`migrate --force`).

### GitHub secrets

Add these under *Settings → Secrets and variables → Actions*:

| Secret | Example / notes |
| --- | --- |
| `SSH_PRIVATE_KEY` | Private key of a key pair whose public key is in the server's `~/.ssh/authorized_keys` |
| `SSH_KNOWN_HOSTS` | Output of `ssh-keyscan -p <port> <host>` |
| `DEPLOY_HOST` | SSH hostname of the server |
| `DEPLOY_USER` | SSH user |
| `DEPLOY_PORT` | Optional, defaults to `22` |
| `DEPLOY_PATH` | Folder on the server to deploy into, e.g. `~/domains/gouwwaas.be` |
| `DEPLOY_PHP_BIN` | Optional, path to PHP on the server if `php` is not the right version |
| `APP_KEY` | Generate with `php artisan key:generate --show` |
| `APP_URL` | `https://gouwwaas.be` |
| `DB_CONNECTION` | Optional, defaults to `mysql` |
| `DB_HOST` | Usually `localhost` when the database is on the same server |
| `DB_PORT` | Optional, defaults to `3306` |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database credentials |
| `MAIL_HOST` | SMTP server for password e-mails, e.g. the host's mail server. Without it, e-mails only go to the log |
| `MAIL_PORT` | Optional, defaults to `587` (use `465` for SSL) |
| `MAIL_USERNAME`, `MAIL_PASSWORD` | SMTP login, usually the mailbox address and its password |
| `MAIL_FROM_ADDRESS` | Optional, defaults to `gouwbureau@gouwwaas.be` |

### One-time server setup

The web server's document root must point to `<DEPLOY_PATH>/current/public`. On
hosting where the document root is a fixed `public_html` folder, replace it with a
symlink after the first deploy:

```sh
cd <DEPLOY_PATH>
mv public_html public_html.bak
ln -s current/public public_html
```

After the first deploy, create a login on the server:

```sh
cd <DEPLOY_PATH>/current && php artisan user:create "Your Name" you@example.com
```

To roll back, re-run the workflow run of an earlier commit in the GitHub *Actions*
tab, or on the server point the `current` symlink at a previous folder in `releases/`.
