# A Turso database driver for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/richan-fongdasen/turso-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/turso-laravel)
[![License: MIT](https://poser.pugx.org/richan-fongdasen/turso-laravel/license.svg)](https://opensource.org/licenses/MIT)
[![PHPStan](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/phpstan.yml)
[![Unit Tests](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/run-tests.yml)
[![Code Style](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/fix-php-code-style-issues.yml/badge.svg?branch=main)](https://github.com/richan-fongdasen/turso-laravel/actions/workflows/fix-php-code-style-issues.yml)
[![codecov](https://codecov.io/gh/richan-fongdasen/turso-laravel/graph/badge.svg?token=eKJSttyUGc)](https://codecov.io/gh/richan-fongdasen/turso-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/richan-fongdasen/turso-laravel.svg?style=flat-square)](https://packagist.org/packages/richan-fongdasen/turso-laravel)

This package provides a Turso database driver for Laravel, allowing you to use Turso as your database backend in Laravel applications. The driver communicates with the Turso database server using an HTTP client.

## Unsupported Features

Some features are not yet supported by this package:

-   Creating and dropping database
-   [Database Transactions](https://turso.tech/blog/bring-your-own-sdk-with-tursos-http-api-ff4ccbed)
-   [Turso Batch Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#execute-a-batch)
-   [Turso Sequence Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#execute-a-sequence-of-sql-statements)
-   [Turso Describe Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#describe-a-statement)
-   [Turso Store SQL Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#store-an-sql-text-on-the-server)
-   [Turso Close Stored SQL Request](https://github.com/tursodatabase/libsql/blob/main/docs/HTTP_V2_SPEC.md#close-a-stored-sql-text)

## Requirements

-   PHP 8.2 or higher
-   Laravel 11.0 or higher
-   Node.js 16 or higher

## Installation

You can install the package via Composer:

```bash
composer require richan-fongdasen/turso-laravel
```

To use Turso as your database driver in Laravel, append the following configuration to the `connections` array in your `config/database.php` file:

```php
'turso' => [
    'driver'                  => 'turso',
    'db_url'                  => env('DB_URL', 'http://localhost:8080'),
    'access_token'            => env('DB_ACCESS_TOKEN'),
    'db_replica'              => env('DB_REPLICA'),
    'database'                => null, // Leave this null
    'prefix'                  => env('DB_PREFIX', ''),
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

### Publishing Configuration and Sync Script

Publish the configuration file and sync script by running the following command:

```bash
php artisan vendor:publish --provider="RichanFongdasen\Turso\TursoLaravelServiceProvider"
```

The above command publishes the following files:

-   `config/turso-laravel.php`
-   `turso-sync.mjs`

The content of the `config/turso-laravel.php` file should look like this:

```php
return [
    'sync_command' => [
        'node_path'       => env('NODE_PATH'),
        'script_filename' => 'turso-sync.mjs',
        'script_path'     => realpath(__DIR__ . '/..'),
        'timeout'         => 60,
    ],
];
```

You may need to set the `NODE_PATH` environment variable to the path of your Node.js executable. This is required to run the sync script.

### Installing Node.js Dependencies

The Turso database driver requires Node.js to run the sync script. Install the Node.js dependencies by running the following command:

```bash
npm install @libsql/client
```

## Configuration

In Laravel applications, the database driver configuration is stored in your `.env` file. Here are the available configurations for the Turso database driver:

```bash
DB_CONNECTION=turso
DB_URL=http://localhost:8080
DB_ACCESS_TOKEN=
DB_REPLICA=
DB_PREFIX=
DB_FOREIGN_KEYS=true
```

| ENV Variable Name | Description                                                                                    |
| :---------------- | :--------------------------------------------------------------------------------------------- |
| DB_URL            | The Turso database server URL. E.g: `https://[databaseName]-[organizationName].turso.io`       |
| DB_ACCESS_TOKEN   | (Optional) The access token to access the Turso database server.                               |
| DB_REPLICA        | (Optional) The full path to the local embedded replica database file. E.g: `/tmp/turso.sqlite` |
| DB_PREFIX         | (Optional) The database table prefix.                                                          |
| DB_FOREIGN_KEYS   | Enable or disable foreign key constraints, default is `true`.                                  |

## Usage

For local development, you can use the local Turso database server provided by the Turso team. Refer to the [Turso CLI documentation](https://docs.turso.tech/local-development#turso-cli) for instructions on running the local Turso database server.

The Turso database driver should work as expected with Laravel's Query Builder and Eloquent ORM. Here are some examples:

```php
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Using Query Builder
$users = DB::table('users')->orderBy('name')->get();

// Using Eloquent ORM
$users = User::with('posts')->orderBy('name')->get();
```

### Embedded Replica Support

The driver supports the embedded replica feature. If you're unfamiliar with this feature, refer to the [Turso embedded replica article](https://turso.tech/blog/introducing-embedded-replicas-deploy-turso-anywhere-2085aa0dc242) for more information.

> If you're using Turso CLI to run the local Turso database server, there is no need to set the `DB_REPLICA` environment variable. The sync script does not work with the local Turso database server.

### Running the sync script from artisan command

Run the sync script manually using the following Artisan command:

```bash
php artisan turso:sync
```

> You may encounter an error if the path to the replica database does not exist. This is expected when the replica database has not been created yet.

### Running the sync script programmatically

Run the sync script programmatically using the following code:

```php
use Illuminate\Support\Facades\DB;
use RichanFongdasen\Turso\Facades\Turso;

if ( DB::hasUpdated() ) {
    // Run the sync script immediately
    Turso::sync();

    // Run the sync script in the background
    Turso::backgroundSync();
}
```

### Disabling/enabling the read replica

If you have configured the read replica in your database configuration, the database connection to the Turso embedded read replica is enabled by default. However, you may want to disable it when performing chained read-write operations directly on the remote database. You can disable the read replica using the following code:

```php
use RichanFongdasen\Turso\Facades\Turso;

// Disable the read replica
Turso::disableReadReplica();

// Re-enable the read replica
// Note that the replica may not contain the latest data
// related to recent write operations performed on the remote database
Turso::enableReadReplica();
```

## Debugging

To debug the HTTP requests and responses sent and received by the Turso database client, enable the debugging feature as follows:

```php
Turso::enableQueryLog();

DB::table('users')->get();

// Get the query log
$logs = Turso::getQueryLog();
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Richan Fongdasen](https://github.com/richan-fongdasen)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
