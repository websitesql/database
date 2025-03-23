<?php

namespace WebsiteSQL\Database\Migration;

use WebsiteSQL\Database\Database;

class MigrationRepository
{
    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $db;

    /**
     * The migrations table name.
     *
     * @var string
     */
    protected string $table = 'migrations';

    /**
     * Create a new migration repository instance.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->ensureMigrationsTableExists();
    }

    /**
     * Ensure the migrations table exists.
     *
     * @return void
     */
    protected function ensureMigrationsTableExists(): void
    {
        $schema = $this->db->getMedoo()->query("SHOW TABLES LIKE '{$this->table}'")->fetchAll();

        if (empty($schema)) {
            $this->db->getMedoo()->query("
                CREATE TABLE {$this->table} (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL
                )
            ");
        }
    }

    /**
     * Get all migrations.
     *
     * @return array
     */
    public function getMigrations(): array
    {
        return $this->db->select($this->table, '*', ['ORDER' => ['id' => 'ASC']]) ?? [];
    }

    /**
     * Get the last migration batch.
     *
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        $lastBatch = $this->db->get($this->table, ['batch'], ['ORDER' => ['batch' => 'DESC']]);
        return $lastBatch ? $lastBatch['batch'] : 0;
    }

    /**
     * Get migrations for a specific batch.
     *
     * @param int $batch
     * @return array
     */
    public function getMigrationsForBatch(int $batch): array
    {
        return $this->db->select($this->table, '*', ['batch' => $batch, 'ORDER' => ['id' => 'DESC']]) ?? [];
    }

    /**
     * Log a migration.
     *
     * @param string $file
     * @param int $batch
     * @return void
     */
    public function log(string $file, int $batch): void
    {
        $this->db->insert($this->table, [
            'migration' => $file,
            'batch' => $batch
        ]);
    }

    /**
     * Remove a migration.
     *
     * @param string $file
     * @return void
     */
    public function delete(string $file): void
    {
        $this->db->delete($this->table, ['migration' => $file]);
    }
}