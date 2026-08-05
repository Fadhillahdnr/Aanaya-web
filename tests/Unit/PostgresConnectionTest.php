<?php

namespace Tests\Unit;

use App\Database\PostgresConnection;
use DateTimeImmutable;
use PDO;
use PHPUnit\Framework\TestCase;

class PostgresConnectionTest extends TestCase
{
    public function test_it_preserves_boolean_bindings_for_postgresql(): void
    {
        $connection = new PostgresConnection(new PDO('sqlite::memory:'));

        $bindings = $connection->prepareBindings([
            true,
            false,
            new DateTimeImmutable('2026-08-05 14:38:51'),
        ]);

        $this->assertTrue($bindings[0]);
        $this->assertFalse($bindings[1]);
        $this->assertSame('2026-08-05 14:38:51', $bindings[2]);
    }
}
