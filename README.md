# WebsiteSQL Database

A powerful database wrapper library built on top of [Medoo](https://medoo.in/), providing a simple and intuitive interface for database operations in PHP applications.

## Installation

```bash
composer require websitesql/database
```

## Basic Usage

### Initialization

```php
// Initialize the database provider
$db = new WebsiteSQL\Database\Database([
    'type' => 'mysql',
    'host' => 'localhost',
    'database' => 'name',
    'username' => 'your_username',
    'password' => 'your_password'
], '../migrations');
```

### CRUD Operations

#### Select Data

```php
// Select all records from users table
$users = $db->select("users", "*");

// Select with conditions
$user = $db->get("users", "*", ["id" => 1]);

// Select with JOIN
$data = $db->select("posts", [
    "[>]users" => ["user_id" => "id"]
], [
    "posts.id",
    "posts.title",
    "users.username"
], [
    "posts.status" => "published",
    "ORDER" => ["posts.created" => "DESC"]
]);
```

#### Insert Data

```php
$db->insert("users", [
    "username" => "john_doe",
    "email" => "john@example.com",
    "created" => date("Y-m-d H:i:s")
]);

// Get last inserted ID
$lastId = $db->id();
```

#### Update Data

```php
$db->update("users", [
    "email" => "new_email@example.com"
], [
    "id" => 1
]);
```

#### Delete Data

```php
$db->delete("users", [
    "id" => 1
]);
```

### Migrations

The database library includes a migration system for managing database schema changes:

```php
// Run migrations
$db->migrations()->up();

// Rollback the last batch of migrations
$db->migrations()->down();

// Rollback all migrations
$db->migrations()->reset();

// Refresh migrations (rollback all and run again)
$db->migrations()->refresh();
```

## API Reference

### Base Operations

- `query(string $statement, array $map = [])`: Execute raw SQL queries
- `exec(string $statement)`: Execute raw statement
- `create(string $table, array $columns, array $options = null)`: Create a table
- `drop(string $table)`: Drop a table
- `select(string $table, array $join, array|string $columns = null, array $where = null)`: Select data
- `get(string $table, array $join, array|string $columns = null, array $where = null)`: Get a single record
- `insert(string $table, array $values, string $primaryKey = null)`: Insert data
- `update(string $table, array $data, array $where = null)`: Update data
- `delete(string $table, array $where = null)`: Delete data
- `replace(string $table, array $columns, array $where = null)`: Replace data

### Aggregation

- `count(string $table, array $join = null, string $column = null, array $where = null)`: Count rows
- `avg(string $table, array $join, string $column = null, array $where = null)`: Calculate average
- `max(string $table, array $join, string $column = null, array $where = null)`: Get maximum value
- `min(string $table, array $join, string $column = null, array $where = null)`: Get minimum value
- `sum(string $table, array $join, string $column = null, array $where = null)`: Calculate sum

### Transactions

- `action(callable $actions)`: Execute callback in transaction
- `beginTransaction()`: Begin a transaction
- `commit()`: Commit a transaction
- `rollBack()`: Rollback a transaction

### Utilities

- `id(?string $name = null)`: Get last inserted ID
- `pdo()`: Get PDO instance
- `debug()`: Get the last query for debugging
- `log()`: Get query log
- `info()`: Get database connection info
- `error()`: Get error information
- `rand(string $table, array $join = null, array|string $columns = null, array $where = null)`: Get random records
- `has(string $table, array $join, array $where = null)`: Check if records exist

### Migrations

- `migrations()->up()`: Run pending migrations
- `migrations()->down()`: Rollback the last batch of migrations
- `migrations()->reset()`: Rollback all migrations
- `migrations()->refresh()`: Rollback all and run again

## License

MIT
