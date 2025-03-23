<?php

namespace WebsiteSQL\Database\Schema;

use WebsiteSQL\Database\Database;

class Schema
{
    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $db;

    /**
     * Create a new schema instance.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Create a new table.
     *
     * @param string $table
     * @param callable $callback
     * @return void
     */
    public function create(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $this->db->raw($blueprint->toSql());
    }

    /**
     * Modify an existing table.
     *
     * @param string $table
     * @param callable $callback
     * @return void
     */
    public function table(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table, true);
        $callback($blueprint);

        foreach ($blueprint->getCommands() as $command) {
            $this->db->raw($command);
        }
    }

    /**
     * Drop a table.
     *
     * @param string $table
     * @return void
     */
    public function drop(string $table): void
    {
        $this->db->raw("DROP TABLE IF EXISTS {$table}");
    }

    /**
     * Rename a table.
     *
     * @param string $from
     * @param string $to
     * @return void
     */
    public function rename(string $from, string $to): void
    {
        $this->db->raw("RENAME TABLE {$from} TO {$to}");
    }

    /**
     * Check if a table exists.
     *
     * @param string $table
     * @return bool
     */
    public function hasTable(string $table): bool
    {
        $result = $this->db->raw("SHOW TABLES LIKE '{$table}'");
        return $result->rowCount() > 0;
    }

    /**
     * Check if a column exists.
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    public function hasColumn(string $table, string $column): bool
    {
        $result = $this->db->raw("SHOW COLUMNS FROM {$table} LIKE '{$column}'");
        return $result->rowCount() > 0;
    }
}