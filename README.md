# WebsiteSQL Database

The database component for the WebsiteSQL framework. This package provides a simple and elegant wrapper around the Medoo database library with added migration functionality.

> **Note:** This package is under active development and is not yet stable. Tests and documentation are still being written and may not be complete.

## Installation

```bash
composer require websitesql/database
```

## Basic Usage

### Configuration

```php
use WebsiteSQL\Database\Database;

$db = new Database([
    'type' => 'mysql',
    'host' => 'localhost',
    'database' => 'database_name',
    'username' => 'username',
    'password' => 'password',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'port' => 3306,
    'prefix' => '',
]);
```

### Querying

```php
// Select all users
$users = $db->select('users', '*');

// Select a single user
$user = $db->get('users', '*', ['id' => 1]);

// Insert a new user
$userId = $db->insert('users', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
]);

// Update a user
$db->update('users', [
    'name' => 'Jane Doe',
], ['id' => 1]);

// Delete a user
$db->delete('users', ['id' => 1]);
```

### Migrations

Create a migration file in your migrations directory:

```php
<?php

use WebsiteSQL\Database\Migration\Migration;

class CreateUsersTable extends Migration
{
    public function up(): void
    {
        $this->schema->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('created_at')->default('CURRENT_TIMESTAMP');
            $table->timestamp('updated_at')->default('CURRENT_TIMESTAMP');
        });
    }

    public function down(): void
    {
        $this->schema->drop('users');
    }
}
```

Run migrations:

```php
// Run all pending migrations
$db->migrate('/path/to/migrations');

// Rollback the last batch of migrations
$db->rollback('/path/to/migrations');

// Reset all migrations
$db->reset('/path/to/migrations');

// Refresh all migrations (reset and re-run)
$db->refresh('/path/to/migrations');
```

## License

The WebsiteSQL Database package is open-sourced software licensed under the [MIT license](LICENSE).
