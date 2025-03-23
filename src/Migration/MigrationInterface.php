<?php

namespace WebsiteSQL\Database\Migration;

interface MigrationInterface
{
    /**
     * Run the migration.
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverse the migration.
     *
     * @return void
     */
    public function down(): void;
}