<?php

namespace WebsiteSQL\Database\Traits;

trait HasQueryBuilder
{
    /**
     * Select data from the database.
     *
     * @param string $table
     * @param array $columns
     * @param array $where
     * @return array|null
     */
    public function select(string $table, array $columns = ['*'], array $where = []): ?array
    {
        return $this->medoo->select($table, $columns, $where);
    }

    /**
     * Get a single record from the database.
     *
     * @param string $table
     * @param array $columns
     * @param array $where
     * @return array|null
     */
    public function get(string $table, array $columns = ['*'], array $where = []): ?array
    {
        $result = $this->medoo->select($table, $columns, $where);
        return $result ? $result[0] : null;
    }

    /**
     * Insert data into the database.
     *
     * @param string $table
     * @param array $data
     * @return int|null
     */
    public function insert(string $table, array $data): ?int
    {
        $this->medoo->insert($table, $data);
        return $this->medoo->id();
    }

    /**
     * Update data in the database.
     *
     * @param string $table
     * @param array $data
     * @param array $where
     * @return int
     */
    public function update(string $table, array $data, array $where): int
    {
        $this->medoo->update($table, $data, $where);
        return $this->medoo->rowCount();
    }

    /**
     * Delete data from the database.
     *
     * @param string $table
     * @param array $where
     * @return int
     */
    public function delete(string $table, array $where): int
    {
        $this->medoo->delete($table, $where);
        return $this->medoo->rowCount();
    }

    /**
     * Execute a raw SQL query.
     *
     * @param string $query
     * @param array $params
     * @return \PDOStatement|null
     */
    public function raw(string $query, array $params = []): ?\PDOStatement
    {
        return $this->medoo->query($query, $params);
    }

    /**
     * Begin a transaction.
     *
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->medoo->pdo->beginTransaction();
    }

    /**
     * Commit a transaction.
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->medoo->pdo->commit();
    }

    /**
     * Rollback a transaction.
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->medoo->pdo->rollBack();
    }
}