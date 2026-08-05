<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        $application = parent::createApplication();

        $connection = $application['config']->get('database.default');
        $database = $application['config']->get("database.connections.{$connection}.database");

        if (! $application->environment('testing') || $connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(
                'Unsafe test database blocked. Tests must use APP_ENV=testing and SQLite :memory:, never Supabase or another persistent database.',
            );
        }

        return $application;
    }
}
