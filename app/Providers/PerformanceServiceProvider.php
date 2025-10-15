<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Optimize database connections for performance
        try {
            DB::statement('SET SESSION sql_mode = ""');
        } catch (\Exception $e) {
            // Ignore if sql_mode cannot be set
        }
        
        // Enable query cache for repeated queries (if supported)
        try {
            DB::statement('SET SESSION query_cache_type = ON');
        } catch (\Exception $e) {
            // Ignore if query cache is not supported
        }
        
        // Optimize MySQL settings for performance
        if (config('app.env') === 'local') {
            // Only apply these optimizations in local environment
            try {
                DB::statement('SET SESSION innodb_flush_log_at_trx_commit = 2');
            } catch (\Exception $e) {
                // Ignore if setting cannot be applied
            }
            
            try {
                DB::statement('SET SESSION sync_binlog = 0');
            } catch (\Exception $e) {
                // Ignore if setting cannot be applied
            }
        }
        
        // Log slow queries in development
        if (config('app.debug')) {
            DB::listen(function ($query) {
                if ($query->time > 100) { // Log queries taking more than 100ms
                    Log::warning('Slow Query Detected', [
                        'sql' => $query->sql,
                        'bindings' => $query->bindings,
                        'time' => $query->time . 'ms'
                    ]);
                }
            });
        }
    }
}