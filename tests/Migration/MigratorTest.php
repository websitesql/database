<?php

namespace WebsiteSQL\Database\Tests\Migration;

use WebsiteSQL\Database\Tests\TestCase;
use WebsiteSQL\Database\Migration\MigrationRepository;
use WebsiteSQL\Database\Migration\Migrator;
use WebsiteSQL\Database\Schema\Schema;

class MigratorTest extends TestCase
{
    private MigrationRepository $repository;
    private Migrator $migrator;
    private string $migrationPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new MigrationRepository($this->db);
        $this->migrator = new Migrator($this->repository, $this->db);
        $this->migrationPath = __DIR__ . '/../migrations';

        // Create migrations directory if it doesn't exist
        if (!is_dir($this->migrationPath)) {
            mkdir($this->migrationPath);
        }

        // Clean up the migrations directory before each test
        $this->cleanMigrationDirectory();
    }

    protected function tearDown(): void
    {
        // Clean up the migrations directory after each test
        $this->cleanMigrationDirectory();
        parent::tearDown();
    }

    private function cleanMigrationDirectory(): void
    {
        $files = glob($this->migrationPath . '/*.php');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function createMigrationFile(string $name, string $content): void
    {
        $filename = $this->migrationPath . '/' . $name . '.php';
        file_put_contents($filename, $content);
    }

    public function testRunMigration(): void
    {
        $migrationName = 'TestMigration';
        $tableName = 'test_table';

        $migrationContent = "<?php
        use WebsiteSQL\Database\Migration\Migration;
        use WebsiteSQL\Database\Schema\Schema;

        class {$migrationName} extends Migration {
            public function up(): void {
                \$this->schema->create('{$tableName}', function (\$table) {
                    \$table->id();
                    \$table->string('name');
                });
            }
            public function down(): void {
                \$this->schema->drop('{$tableName}');
            }
        }";

        $this->createMigrationFile($migrationName, $migrationContent);

        $migrations = $this->migrator->run($this->migrationPath);

        $this->assertCount(1, $migrations);
        $this->assertEquals($migrationName, str_replace('.php', '', $migrations[0]));
        $this->assertTrue((new Schema($this->db))->hasTable($tableName));
    }

    public function testRollbackMigration(): void
    {
        $migrationName = 'TestMigration';
        $tableName = 'test_table';

        $migrationContent = "<?php
        use WebsiteSQL\Database\Migration\Migration;
        use WebsiteSQL\Database\Schema\Schema;

        class {$migrationName} extends Migration {
            public function up(): void {
                \$this->schema->create('{$tableName}', function (\$table) {
                    \$table->id();
                    \$table->string('name');
                });
            }
            public function down(): void {
                \$this->schema->drop('{$tableName}');
            }
        }";

        $this->createMigrationFile($migrationName, $migrationContent);

        $this->migrator->run($this->migrationPath);
        $this->assertTrue((new Schema($this->db))->hasTable($tableName));

        $rolledBack = $this->migrator->rollback($this->migrationPath);

        $this->assertCount(1, $rolledBack);
        $this->assertEquals($migrationName, str_replace('.php', '', $rolledBack[0]));
        $this->assertFalse((new Schema($this->db))->hasTable($tableName));
    }

    public function testResetMigrations(): void
    {
        $migrationName = 'TestMigration';
        $tableName = 'test_table';

        $migrationContent = "<?php
        use WebsiteSQL\Database\Migration\Migration;
        use WebsiteSQL\Database\Schema\Schema;

        class {$migrationName} extends Migration {
            public function up(): void {
                \$this->schema->create('{$tableName}', function (\$table) {
                    \$table->id();
                    \$table->string('name');
                });
            }
            public function down(): void {
                \$this->schema->drop('{$tableName}');
            }
        }";

        $this->createMigrationFile($migrationName, $migrationContent);

        $this->migrator->run($this->migrationPath);
        $this->assertTrue((new Schema($this->db))->hasTable($tableName));

        $resetMigrations = $this->migrator->reset($this->migrationPath);

        $this->assertCount(1, $resetMigrations);
        $this->assertEquals($migrationName, str_replace('.php', '', $resetMigrations[0]));
        $this->assertFalse((new Schema($this->db))->hasTable($tableName));
    }
}