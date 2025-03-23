<?php

namespace WebsiteSQL\Database\Migration;

use WebsiteSQL\Database\Database;

class Migrator
{
    /**
     * The migration repository instance.
     *
     * @var MigrationRepository
     */
    protected MigrationRepository $repository;

    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $db;

    /**
     * Create a new migrator instance.
     *
     * @param MigrationRepository $repository
     * @param Database $db
     */
    public function __construct(MigrationRepository $repository, Database $db)
    {
        $this->repository = $repository;
        $this->db = $db;
    }

    /**
     * Run the migrations.
     *
     * @param string $path
     * @return array
     */
    public function run(string $path): array
    {
        $files = $this->getMigrationFiles($path);
        $ran = $this->getRanMigrations();
        $migrations = array_diff($files, $ran);

        if (empty($migrations)) {
            return [];
        }

        $batch = $this->repository->getLastBatchNumber() + 1;
        $migrated = [];

        foreach ($migrations as $file) {
            $this->runMigration($path, $file, $batch);
            $migrated[] = $file;
        }

        return $migrated;
    }

    /**
     * Rollback the last migration batch.
     *
     * @param string $path
     * @param int $steps
     * @return array
     */
    public function rollback(string $path, int $steps = 1): array
    {
        $migrations = [];
        
        for ($i = 0; $i < $steps; $i++) {
            $batch = $this->repository->getLastBatchNumber() - $i;
            
            if ($batch < 1) {
                break;
            }
            
            $batchMigrations = $this->repository->getMigrationsForBatch($batch);
            
            foreach ($batchMigrations as $migration) {
                $this->rollbackMigration($path, $migration['migration']);
                $migrations[] = $migration['migration'];
            }
        }

        return $migrations;
    }

    /**
     * Reset all migrations.
     *
     * @param string $path
     * @return array
     */
    public function reset(string $path): array
    {
        $migrations = array_reverse($this->getRanMigrations());
        $rolledBack = [];

        foreach ($migrations as $migration) {
            $this->rollbackMigration($path, $migration);
            $rolledBack[] = $migration;
        }

        return $rolledBack;
    }

    /**
     * Run a migration.
     *
     * @param string $path
     * @param string $file
     * @param int $batch
     * @return void
     */
    protected function runMigration(string $path, string $file, int $batch): void
    {
        $migration = $this->resolveMigration($path, $file);
        $migration->up();

        $this->repository->log($file, $batch);
    }

    /**
     * Rollback a migration.
     *
     * @param string $path
     * @param string $file
     * @return void
     */
    protected function rollbackMigration(string $path, string $file): void
    {
        $migration = $this->resolveMigration($path, $file);
        $migration->down();

        $this->repository->delete($file);
    }

    /**
     * Resolve a migration instance.
     *
     * @param string $path
     * @param string $file
     * @return MigrationInterface
     */
    protected function resolveMigration(string $path, string $file): MigrationInterface
    {
        $class = $this->getMigrationClass($file);
        require_once $path . '/' . $file . '.php';
        
        return new $class($this->db);
    }

    /**
     * Get the migration class name.
     *
     * @param string $file
     * @return string
     */
    protected function getMigrationClass(string $file): string
    {
        return studly_case(str_replace('.php', '', $file));
    }

    /**
     * Get all migration files.
     *
     * @param string $path
     * @return array
     */
    protected function getMigrationFiles(string $path): array
    {
        $files = scandir($path);
        $migrations = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !str_ends_with($file, '.php')) {
                continue;
            }

            $migrations[] = str_replace('.php', '', $file);
        }

        return $migrations;
    }

    /**
     * Get ran migrations.
     *
     * @return array
     */
    protected function getRanMigrations(): array
    {
        $migrations = $this->repository->getMigrations();
        return array_column($migrations, 'migration');
    }
}