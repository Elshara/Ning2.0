# Ning2.0

This codebase is in the process of being modernized to run on current versions of PHP.

## Getting started

1. Ensure you are using PHP 8.2 or newer (PHP 8.4 ready).
2. Install Composer dependencies:
   ```bash
   composer install
   ```
3. Serve the application through your preferred PHP-compatible web server.

Composer now generates a classmap for the legacy `lib/` and `widgets/` directories, which allows
new services and utilities to be introduced incrementally without relying on global `require`
statements.
