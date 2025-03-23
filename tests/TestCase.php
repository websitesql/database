<?php

namespace WebsiteSQL\Database\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use WebsiteSQL\Database\Database;

class TestCase extends BaseTestCase
{
    protected Database $db;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup a SQLite in-memory database for testing
        $config = [
            'type' => 'sqlite',
            'database' => ':memory:',
        ];

        $this->db = new Database($config);

        // Create a simple table for testing
        $this->db->raw("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE
            )
        ");
    }

    protected function tearDown(): void
    {
        // Drop the users table after each test
        $this->db->raw("DROP TABLE IF EXISTS users");

        parent::tearDown();
    }
}