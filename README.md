# Ning2.0

This codebase is in the process of being modernized to run on current versions of PHP.

## Getting started

1. Ensure you are using PHP 8.2 or newer (the codebase is continuously verified against PHP 8.4).
2. Install Composer dependencies:
   ```bash
   composer install
   ```
3. Upload or serve the application through your preferred PHP-compatible web server and visit the
   root URL in your browser. The guided setup wizard will launch automatically if no configuration
   exists.
4. Follow the wizard to provide your site details, database credentials, and the initial
   administrator account. The installer validates the MySQL connection, provisions the database if
   needed, and writes a `config/app.php` file (excluded from version control) with the detected
   environment defaults.
5. After completion you will be redirected back to the application bootstrap, ready to sign in with
   the administrator credentials you supplied.

## Setup wizard overview

The installer captures the following information one step at a time while detecting as many
defaults as possible from the current request:

1. **Environment** – confirms the detected host, port, and HTTPS status, lets you name the network,
   and opt into HTTPS enforcement.
2. **Database** – gathers the MySQL host, port, database name, and credentials, verifies the
   connection using PDO, and creates the database if it is missing.
3. **Administrator** – provisions the initial administrator by storing the email and a securely
   hashed password in the configuration.
4. **Finalize** – summarizes the selections and writes the generated configuration to
   `config/app.php`.

Composer now generates a classmap for the legacy `lib/` and `widgets/` directories, which allows
new services and utilities to be introduced incrementally without relying on global `require`
statements.
