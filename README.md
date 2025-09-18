# Ning2.0

This codebase is in the process of being modernized to run on current versions of PHP.

## Getting started

1. Ensure you are using PHP 8.2 or newer (the codebase is continuously verified against PHP 8.4) and
   that the `pdo_mysql`, `mbstring`, and `openssl` extensions are available.
2. Upload or extract the repository into the document root of your preferred PHP-compatible web
   server. All required libraries are included with the project.

3. (Optional) Run `composer install` from the project root to prime the autoloader and benefit from
   classmap optimisations when additional packages are introduced.
4. Confirm the `config/` directory (or whichever location you deploy as your application root)
   is writable by the PHP process so the installer can persist `config/app.php`.
5. Visit the root URL in your browser. The guided setup wizard launches automatically when no
   configuration exists.
6. Complete the wizard to describe your environment, connect to MySQL, define the global super
   administrator, appoint the first network administrator, and choose the addressing and automation
   strategy for the initial network.
7. After completion the installer writes `config/app.php` (ignored by version control) and redirects

3. Confirm the `config/` directory (or whichever location you deploy as your application root)
   is writable by the PHP process so the installer can persist `config/app.php`.
4. Visit the root URL in your browser. The guided setup wizard launches automatically when no
   configuration exists.
5. Complete the wizard to describe your environment, connect to MySQL, define the global super
   administrator, appoint the first network administrator, and choose the addressing and automation
   strategy for the initial network.
6. After completion the installer writes `config/app.php` (ignored by version control) and redirects

   back to the application bootstrap so you can sign in as either the super administrator or the
   network owner.

## Setup wizard overview

The installer walks through five focused steps while detecting as many defaults as possible from the
current request:

1. **Environment** – confirms the detected host, port, HTTPS status, and recommended base domain
   while allowing you to name the overall platform.
2. **Database** – gathers the MySQL host, port, database name, and credentials, verifies access
   using PDO, and creates the database when it is missing.
3. **Administrators** – provisions both a global super administrator and the initial network
   administrator with securely hashed passwords.
4. **Network & Automation** – defines the primary network name, subdomain or base path, alias
   domains, automatic update defaults, and the background task scheduler behaviour.
5. **Finalize** – summarizes the selections and writes the generated configuration file.

Once installation is complete the platform supports multiple networks with unique domains or
subdomains, per-network administrators, optional alias domains, GitHub-driven automatic deployments
with opt-out controls, and either cron- or page-triggered scheduled tasks.


## Static assets

- Legacy stylesheets now live under `assets/css/`. Update templates or themes that previously
  referenced files in the repository root to load them from the consolidated directory. Future
  asset types (JavaScript, images) should adopt sibling folders to keep the structure shallow and
  predictable.

The legacy runtime now bootstraps a shared PDO connection using the generated configuration and
stores the connection metadata in `$GLOBALS['nf_runtime']['database']`. Individual modules should
prefer this factory over bespoke MySQL adapters as database modernization continues.


