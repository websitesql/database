<?php

namespace WebsiteSQL\Database\Tests\Schema;

use WebsiteSQL\Database\Tests\TestCase;
use WebsiteSQL\Database\Schema\Schema;

class SchemaTest extends TestCase
{
    private Schema $schema;

    protected function setUp(): void
    {
        parent::setUp();
        $this->schema = new Schema($this->db);
    }

    public function testCreateTable(): void
    {
        $tableName = 'test_table';
        $this->schema->create($tableName, function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
        });

        $this->assertTrue($this->schema->hasTable($tableName));
        $this->assertTrue($this->schema->hasColumn($tableName, 'id'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'name'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'email'));
    }

    public function testDropTable(): void
    {
        $tableName = 'test_table';
        $this->schema->create($tableName, function ($table) {
            $table->id();
            $table->string('name');
        });

        $this->assertTrue($this->schema->hasTable($tableName));

        $this->schema->drop($tableName);

        $this->assertFalse($this->schema->hasTable($tableName));
    }

    public function testRenameTable(): void
    {
        $oldTableName = 'old_table';
        $newTableName = 'new_table';

        $this->schema->create($oldTableName, function ($table) {
            $table->id();
            $table->string('name');
        });

        $this->assertTrue($this->schema->hasTable($oldTableName));

        $this->schema->rename($oldTableName, $newTableName);

        $this->assertTrue($this->schema->hasTable($newTableName));
        $this->assertFalse($this->schema->hasTable($oldTableName));
    }

    public function testHasTable(): void
    {
        $tableName = 'test_table';
        $this->schema->create($tableName, function ($table) {
            $table->id();
            $table->string('name');
        });

        $this->assertTrue($this->schema->hasTable($tableName));
        $this->assertFalse($this->schema->hasTable('non_existent_table'));
    }

    public function testHasColumn(): void
    {
        $tableName = 'test_table';
        $this->schema->create($tableName, function ($table) {
            $table->id();
            $table->string('name');
        });

        $this->assertTrue($this->schema->hasColumn($tableName, 'id'));
        $this->assertTrue($this->schema->hasColumn($tableName, 'name'));
        $this->assertFalse($this->schema->hasColumn($tableName, 'non_existent_column'));
    }
}