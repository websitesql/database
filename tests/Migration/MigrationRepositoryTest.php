<?php

namespace WebsiteSQL\Database\Tests\Migration;

use WebsiteSQL\Database\Tests\TestCase;
use WebsiteSQL\Database\Migration\MigrationRepository;

class MigrationRepositoryTest extends TestCase
{
    private MigrationRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MigrationRepository($this->db);
    }

    public function testGetMigrations(): void
    {
        $this->db->insert('migrations', ['migration' => 'test_migration', 'batch' => 1]);
        $migrations = $this->repository->getMigrations();
        $this->assertIsArray($migrations);
        $this->assertCount(1, $migrations);
    }

    public function testGetLastBatchNumber(): void
    {
        $this->db->insert('migrations', ['migration' => 'test_migration', 'batch' => 1]);
        $this->db->insert('migrations', ['migration' => 'test_migration_2', 'batch' => 2]);
        $lastBatch = $this->repository->getLastBatchNumber();
        $this->assertEquals(2, $lastBatch);
    }

    public function testGetMigrationsForBatch(): void
    {
        $this->db->insert('migrations', ['migration' => 'test_migration', 'batch' => 1]);
        $this->db->insert('migrations', ['migration' => 'test_migration_2', 'batch' => 2]);
        $migrations = $this->repository->getMigrationsForBatch(1);
        $this->assertIsArray($migrations);
        $this->assertCount(1, $migrations);
        $this->assertEquals('test_migration', $migrations[0]['migration']);
    }

    public function testLogMigration(): void
    {
        $this->repository->log('test_migration', 1);
        $migrations = $this->db->select('migrations', ['migration', 'batch'], ['migration' => 'test_migration']);
        $this->assertIsArray($migrations);
        $this->assertCount(1, $migrations);
        $this->assertEquals('test_migration', $migrations[0]['migration']);
        $this->assertEquals(1, $migrations[0]['batch']);
    }

    public function testDeleteMigration(): void
    {
        $this->db->insert('migrations', ['migration' => 'test_migration', 'batch' => 1]);
        $this->repository->delete('test_migration');
        $migrations = $this->db->select('migrations', ['migration', 'batch'], ['migration' => 'test_migration']);
        $this->assertEmpty($migrations);
    }
}