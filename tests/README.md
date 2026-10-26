# Validation

Use an isolated Joomla test installation with PHP 8.3+, a supported database, Cassiopeia as the site template, Atum for administration and this module installed. These tests create development data and are not intended for production sites.

From the Joomla root, first run `php /path/to/tests/seed.php`. This creates three public fixture articles and seven published module layouts idempotently. `seed.php` currently uses MySQL/MariaDB PDO and the site's configuration without printing credentials.

Run `php /path/to/tests/integration.php` from the Joomla root (or set `JOOMLA_ROOT`). Its 34 assertions cover image modes, compact/pretty/escaped JSON, malformed/empty image data, alt-text fallback, result counts, ordering, featured schedules and missing-carousel fallback. Temporary changes use the injected Joomla database connection and are rolled back on success or failure. Assertion failures exit nonzero. Run this only on the isolated fixture site.

For browser checks, Python Playwright and Chromium are required. Export `JOOMLA_TEST_ADMIN_PASSWORD` securely and optionally `JOOMLA_TEST_ADMIN_USERNAME` (default `reviewadmin`). Never commit these values. `JOOMLA_TEST_MATRIX` is an optional JSON list of `[version, local_port]` pairs; defaults are 6.1.1:8111, 6.1.4:8114 and 6.2.0:8120. The admin account must have permission to manage site modules.

Run `python3 tests/browser-smoke.py`. It verifies all seven layouts, actual carousel advancement, asset delivery, seven JSON-LD ItemLists, an Atum module edit form and no JavaScript errors. Set `JOOMLA_TEST_RESULT_FILE` to save JSON results outside the source tree if needed.

The release was tested on Joomla 6.1.1, 6.1.4 and 6.2.0 with PHP 8.3.32 and MariaDB 11.4. PostgreSQL execution and third-party templates have not been tested.
