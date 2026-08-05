<?php

namespace App\Database;

use DateTimeInterface;
use Illuminate\Database\PostgresConnection as LaravelPostgresConnection;
use PDO;

class PostgresConnection extends LaravelPostgresConnection
{
    /**
     * Preserve booleans until PDO binds them as PostgreSQL booleans.
     *
     * Laravel's base connection converts booleans to integers. PostgreSQL
     * rejects those integers for native boolean columns.
     */
    public function prepareBindings(array $bindings): array
    {
        $dateFormat = $this->getQueryGrammar()->getDateFormat();

        foreach ($bindings as $key => $value) {
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($dateFormat);
            }
        }

        return $bindings;
    }

    public function bindValues($statement, $bindings): void
    {
        foreach ($bindings as $key => $value) {
            $statement->bindValue(
                is_string($key) ? $key : $key + 1,
                $value,
                match (true) {
                    is_bool($value) => PDO::PARAM_BOOL,
                    is_int($value) => PDO::PARAM_INT,
                    is_resource($value) => PDO::PARAM_LOB,
                    default => PDO::PARAM_STR,
                },
            );
        }
    }
}
