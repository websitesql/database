<?php

namespace WebsiteSQL\Database\Tests;

use WebsiteSQL\Database\Database;

class DatabaseTest extends TestCase
{
    public function testConnection(): void
    {
        $this->assertInstanceOf(Database::class, $this->db);
    }

    public function testSelect(): void
    {
        // Insert a user for testing
        $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $users = $this->db->select('users', ['id', 'name', 'email']);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users[0]['name']);
    }

    public function testGet(): void
    {
        // Insert a user for testing
        $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $user = $this->db->get('users', ['id', 'name', 'email'], ['id' => 1]);

        $this->assertIsArray($user);
        $this->assertEquals('John Doe', $user['name']);
    }

    public function testInsert(): void
    {
        $userId = $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $this->assertIsNumeric($userId);
        $this->assertGreaterThan(0, $userId);
    }

    public function testUpdate(): void
    {
        // Insert a user for testing
        $userId = $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $updatedRows = $this->db->update('users', ['name' => 'Jane Doe'], ['id' => $userId]);

        $this->assertEquals(1, $updatedRows);

        $user = $this->db->get('users', ['id', 'name', 'email'], ['id' => $userId]);
        $this->assertEquals('Jane Doe', $user['name']);
    }

    public function testDelete(): void
    {
        // Insert a user for testing
        $userId = $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $deletedRows = $this->db->delete('users', ['id' => $userId]);

        $this->assertEquals(1, $deletedRows);

        $user = $this->db->get('users', ['id', 'name', 'email'], ['id' => $userId]);
        $this->assertNull($user);
    }

    public function testRawQuery(): void
    {
        // Insert a user for testing
        $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);

        $result = $this->db->raw("SELECT * FROM users WHERE name = 'John Doe'");
        $users = $result->fetchAll(\PDO::FETCH_ASSOC);

        $this->assertIsArray($users);
        $this->assertCount(1, $users);
        $this->assertEquals('John Doe', $users[0]['name']);
    }

    public function testTransaction(): void
    {
        $this->db->beginTransaction();
        $this->db->insert('users', ['name' => 'John Doe', 'email' => 'john@example.com']);
        $this->db->commit();

        $user = $this->db->get('users', ['id', 'name', 'email'], ['name' => 'John Doe']);
        $this->assertNotNull($user);

        $this->db->beginTransaction();
        $this->db->insert('users', ['name' => 'Jane Doe', 'email' => 'jane@example.com']);
        $this->db->rollback();

        $user = $this->db->get('users', ['id', 'name', 'email'], ['name' => 'Jane Doe']);
        $this->assertNull($user);
    }
}