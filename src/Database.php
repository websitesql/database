<?php

namespace WebsiteSQL\Database;

use Medoo\Medoo;
use WebsiteSQL\Database\Migration\MigrationRepository;
use WebsiteSQL\Database\Migration\Migrator;
use WebsiteSQL\Database\Traits\HasQueryBuilder;

class Database
{
    use HasQueryBuilder;

    /**
     * The Medoo instance.
     *
     * @var Medoo
     */
    protected Medoo $medoo;

    /**
     * The migration repository instance.
     *
     * @var MigrationRepository
     */
    protected MigrationRepository $migrationRepository;

    /**
     * The migrator instance.
     *
     * @var Migrator
     */
    protected Migrator $migrator;

    /**
     * Create a new database instance.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->medoo = new Medoo($config);
        $this->migrationRepository = new MigrationRepository($this);
        $this->migrator = new Migrator($this->migrationRepository, $this);
    }

    /**
     * Get the Medoo instance.
     *
     * @return Medoo
     */
    public function getMedoo(): Medoo
    {
        return $this->medoo;
    }

    /**
     * Get the migrator instance.
     *
     * @return Migrator
     */
    public function getMigrator(): Migrator
    {
        return $this->migrator;
    }

    /**
     * Run database migrations.
     *
     * @param string $path
     * @return array
     */
    public function migrate(string $path): array
    {
        return $this->migrator->run($path);
    }

    /**
     * Rollback the last database migration.
     *
     * @param string $path
     * @param int $steps
     * @return array
     */
    public function rollback(string $path, int $steps = 1): array
    {
        return $this->migrator->rollback($path, $steps);
    }

    /**
     * Reset all migrations.
     *
     * @param string $path
     * @return array
     */
    public function reset(string $path): array
    {
        return $this->migrator->reset($path);
    }

    /**
     * Refresh all migrations.
     *
     * @param string $path
     * @return array
     */
    public function refresh(string $path): array
    {
        $this->reset($path);
        return $this->migrate($path);
    }

    /**
     * Proxy method calls to Medoo.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->medoo->$method(...$arguments);
    }
}