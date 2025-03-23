<?php

namespace WebsiteSQL\Database\Schema;

class Blueprint
{
    /**
     * The table the blueprint describes.
     *
     * @var string
     */
    protected string $table;

    /**
     * Whether the blueprint is for an existing table.
     *
     * @var bool
     */
    protected bool $existing;

    /**
     * The columns that should be added to the table.
     *
     * @var array
     */
    protected array $columns = [];

    /**
     * The commands that should be run for the table.
     *
     * @var array
     */
    protected array $commands = [];

    /**
     * The storage engine that should be used for the table.
     *
     * @var string
     */
    protected string $engine = 'InnoDB';

    /**
     * The default character set that should be used for the table.
     */
    protected string $charset = 'utf8mb4';

    /**
     * The collation that should be used for the table.
     */
    protected string $collation = 'utf8mb4_unicode_ci';

    /**
     * Create a new schema blueprint.
     *
     * @param string $table
     * @param bool $existing
     */
    public function __construct(string $table, bool $existing = false)
    {
        $this->table = $table;
        $this->existing = $existing;
    }

    /**
     * Get the commands.
     *
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Add a new column to the blueprint.
     *
     * @param string $type
     * @param string $name
     * @param array $parameters
     * @return $this
     */
    protected function addColumn(string $type, string $name, array $parameters = []): self
    {
        $this->columns[] = array_merge(compact('type', 'name'), $parameters);

        return $this;
    }

    /**
     * Create a new auto-incrementing integer column.
     *
     * @param string $name
     * @return $this
     */
    public function id(string $name = 'id'): self
    {
        return $this->addColumn('INT', $name, [
            'unsigned' => true,
            'autoIncrement' => true,
            'primary' => true,
        ]);
    }

    /**
     * Create a new string column.
     *
     * @param string $name
     * @param int $length
     * @return $this
     */
    public function string(string $name, int $length = 255): self
    {
        return $this->addColumn('VARCHAR', $name, ['length' => $length]);
    }

    /**
     * Create a new text column.
     *
     * @param string $name
     * @return $this
     */
    public function text(string $name): self
    {
        return $this->addColumn('TEXT', $name);
    }

    /**
     * Create a new integer column.
     *
     * @param string $name
     * @param bool $unsigned
     * @return $this
     */
    public function integer(string $name, bool $unsigned = false): self
    {
        return $this->addColumn('INT', $name, ['unsigned' => $unsigned]);
    }

    /**
     * Create a new big integer column.
     *
     * @param string $name
     * @param bool $unsigned
     * @return $this
     */
    public function bigInteger(string $name, bool $unsigned = false): self
    {
        return $this->addColumn('BIGINT', $name, ['unsigned' => $unsigned]);
    }

    /**
     * Create a new float column.
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return $this
     */
    public function float(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->addColumn('FLOAT', $name, [
            'precision' => $precision,
            'scale' => $scale,
        ]);
    }

    /**
     * Create a new decimal column.
     *
     * @param string $name
     * @param int $precision
     * @param int $scale
     * @return $this
     */
    public function decimal(string $name, int $precision = 8, int $scale = 2): self
    {
        return $this->addColumn('DECIMAL', $name, [
            'precision' => $precision,
            'scale' => $scale,
        ]);
    }

    /**
     * Create a new boolean column.
     *
     * @param string $name
     * @return $this
     */
    public function boolean(string $name): self
    {
        return $this->addColumn('TINYINT', $name, ['length' => 1]);
    }

    /**
     * Create a new date column.
     *
     * @param string $name
     * @return $this
     */
    public function date(string $name): self
    {
        return $this->addColumn('DATE', $name);
    }

    /**
     * Create a new datetime column.
     *
     * @param string $name
     * @return $this
     */
    public function dateTime(string $name): self
    {
        return $this->addColumn('DATETIME', $name);
    }

    /**
     * Create a new timestamp column.
     *
     * @param string $name
     * @return $this
     */
    public function timestamp(string $name): self
    {
        return $this->addColumn('TIMESTAMP', $name);
    }

    /**
     * Add nullable modifier to the column.
     *
     * @return $this
     */
    public function nullable(): self
    {
        $this->columns[count($this->columns) - 1]['nullable'] = true;

        return $this;
    }

    /**
     * Add default value to the column.
     *
     * @param mixed $value
     * @return $this
     */
    public function default($value): self
    {
        $this->columns[count($this->columns) - 1]['default'] = $value;

        return $this;
    }

    /**
     * Add a unique index.
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function unique($columns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $name = $name ?: $this->table . '_' . implode('_', $columns) . '_unique';

        if ($this->existing) {
            $this->commands[] = "ALTER TABLE {$this->table} ADD CONSTRAINT {$name} UNIQUE (" . implode(', ', $columns) . ")";
        } else {
            $this->columns[] = [
                'type' => 'index',
                'name' => $name,
                'columns' => $columns,
                'unique' => true,
            ];
        }

        return $this;
    }

    /**
     * Add a primary key.
     *
     * @param string|array $columns
     * @param string|null $name
     * @return $this
     */
    public function primary($columns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $name = $name ?: $this->table . '_' . implode('_', $columns) . '_primary';

        if ($this->existing) {
            $this->commands[] = "ALTER TABLE {$this->table} ADD CONSTRAINT {$name} PRIMARY KEY (" . implode(', ', $columns) . ")";
        } else {
            $this->columns[] = [
                'type' => 'index',
                'name' => $name,
                'columns' => $columns,
                'primary' => true,
            ];
        }

        return $this;
    }

    /**
     * Add a foreign key.
     *
     * @param string|array $columns
     * @param string $referencedTable
     * @param string|array $referencedColumns
     * @param string|null $name
     * @return $this
     */
    public function foreign($columns, string $referencedTable, $referencedColumns, ?string $name = null): self
    {
        $columns = is_array($columns) ? $columns : [$columns];
        $referencedColumns = is_array($referencedColumns) ? $referencedColumns : [$referencedColumns];
        $name = $name ?: $this->table . '_' . implode('_', $columns) . '_foreign';

        if ($this->existing) {
            $this->commands[] = "ALTER TABLE {$this->table} ADD CONSTRAINT {$name} FOREIGN KEY (" . implode(', ', $columns) . ") REFERENCES {$referencedTable} (" . implode(', ', $referencedColumns) . ")";
        } else {
            $this->columns[] = [
                'type' => 'foreign',
                'name' => $name,
                'columns' => $columns,
                'referencedTable' => $referencedTable,
                'referencedColumns' => $referencedColumns,
            ];
        }

        return $this;
    }

    /**
     * Add a new column to an existing table.
     *
     * @param string $name
     * @param string $type
     * @param array $parameters
     * @return $this
     */
    public function addColumnToTable(string $name, string $type, array $parameters = []): self
    {
        $column = $this->getColumnDefinition(array_merge(compact('type', 'name'), $parameters));
        $this->commands[] = "ALTER TABLE {$this->table} ADD COLUMN {$column}";

        return $this;
    }

    /**
     * Drop a column from an existing table.
     *
     * @param string $name
     * @return $this
     */
    public function dropColumn(string $name): self
    {
        $this->commands[] = "ALTER TABLE {$this->table} DROP COLUMN {$name}";

        return $this;
    }

    /**
     * Rename a column.
     *
     * @param string $from
     * @param string $to
     * @param string $type
     * @param array $parameters
     * @return $this
     */
    public function renameColumn(string $from, string $to, string $type, array $parameters = []): self
    {
        $column = $this->getColumnDefinition(array_merge(compact('type'), ['name' => $to], $parameters));
        $this->commands[] = "ALTER TABLE {$this->table} CHANGE {$from} {$column}";

        return $this;
    }

    /**
     * Get the SQL statement for creating the table.
     *
     * @return string
     */
    public function toSql(): string
    {
        $columns = [];
        $indexes = [];
        $foreignKeys = [];

        foreach ($this->columns as $column) {
            if (isset($column['type']) && $column['type'] === 'index') {
                $indexes[] = $this->getIndexDefinition($column);
            } elseif (isset($column['type']) && $column['type'] === 'foreign') {
                $foreignKeys[] = $this->getForeignKeyDefinition($column);
            } else {
                $columns[] = $this->getColumnDefinition($column);
            }
        }

        $sql = "CREATE TABLE {$this->table} (\n    " . implode(",\n    ", array_merge($columns, $indexes, $foreignKeys)) . "\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collation}";

        return $sql;
    }

    /**
     * Get the column definition.
     *
     * @param array $column
     * @return string
     */
    protected function getColumnDefinition(array $column): string
    {
        $definition = "{$column['name']} {$column['type']}";

        if (isset($column['length'])) {
            $definition .= "({$column['length']})";
        } elseif (isset($column['precision']) && isset($column['scale'])) {
            $definition .= "({$column['precision']}, {$column['scale']})";
        }

        if (isset($column['unsigned']) && $column['unsigned']) {
            $definition .= ' UNSIGNED';
        }

        if (isset($column['nullable']) && $column['nullable']) {
            $definition .= ' NULL';
        } else {
            $definition .= ' NOT NULL';
        }

        if (isset($column['autoIncrement']) && $column['autoIncrement']) {
            $definition .= ' AUTO_INCREMENT';
        }

        if (isset($column['default'])) {
            $definition .= ' DEFAULT ' . (is_string($column['default']) ? "'{$column['default']}'" : $column['default']);
        }

        if (isset($column['primary']) && $column['primary']) {
            $definition .= ' PRIMARY KEY';
        }

        return $definition;
    }

    /**
     * Get the index definition.
     *
     * @param array $index
     * @return string
     */
    protected function getIndexDefinition(array $index): string
    {
        $type = '';

        if (isset($index['primary']) && $index['primary']) {
            $type = 'PRIMARY KEY';
        } elseif (isset($index['unique']) && $index['unique']) {
            $type = "UNIQUE INDEX {$index['name']}";
        } else {
            $type = "INDEX {$index['name']}";
        }

        return "{$type} (" . implode(', ', $index['columns']) . ")";
    }

    /**
     * Get the foreign key definition.
     *
     * @param array $foreignKey
     * @return string
     */
    protected function getForeignKeyDefinition(array $foreignKey): string
    {
        return "CONSTRAINT {$foreignKey['name']} FOREIGN KEY (" . implode(', ', $foreignKey['columns']) . ") REFERENCES {$foreignKey['referencedTable']} (" . implode(', ', $foreignKey['referencedColumns']) . ")";
    }
}