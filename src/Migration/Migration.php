<?php

namespace WebsiteSQL\Database\Migration;

use WebsiteSQL\Database\Database;
use WebsiteSQL\Database\Schema\Schema;

abstract class Migration implements MigrationInterface
{
    /**
     * The database instance.
     *
     * @var Database
     */
    protected Database $db;

    /**
     * The schema builder instance.
     *
     * @var Schema
     */
    protected Schema $schema;

    /**
     * Create a new migration instance.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->schema = new Schema($db);
    }
}