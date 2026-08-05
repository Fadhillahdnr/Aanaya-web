<?php

namespace App\Providers;

use App\Database\PostgresConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Connection::resolverFor(
            'pgsql',
            fn ($connection, $database, $prefix, $config) => new PostgresConnection(
                $connection,
                $database,
                $prefix,
                $config,
            ),
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $defaultConnection = config('database.default');
        $databaseHost = (string) config("database.connections.{$defaultConnection}.host", '');
        $usesSupabase = str_ends_with(strtolower($databaseHost), '.supabase.com');

        DB::prohibitDestructiveCommands(app()->isProduction() || $usesSupabase);
    }
}
