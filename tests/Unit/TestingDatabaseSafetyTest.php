<?php

namespace Tests\Unit;

use Tests\TestCase;

class TestingDatabaseSafetyTest extends TestCase
{
    public function test_automated_tests_use_an_isolated_in_memory_database(): void
    {
        $this->assertTrue(app()->environment('testing'));
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame('array', config('cache.default'));
        $this->assertSame('array', config('session.driver'));
        $this->assertSame('sync', config('queue.default'));
    }
}
