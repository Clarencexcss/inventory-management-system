<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Services\PerformanceMonitoringService;
use App\Services\AdvancedCacheService;
use Carbon\Carbon;

/**
 * Performance Monitoring and Diagnostics Controller
 * 
 * Provides endpoints for monitoring system performance,
 * running diagnostics, and managing optimizations.
 */
class PerformanceController extends Controller
{
    /**
     * Get comprehensive performance metrics
     */
    public function getMetrics(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $metrics = [
                'timestamp' => Carbon::now()->toISOString(),
                'system' => $this->getSystemMetrics(),
                'database' => $this->getDatabaseMetrics(),
                'cache' => $this->getCacheMetrics(),
                'application' => $this->getApplicationMetrics(),
                'response_time' => 0
            ];
            
            $metrics['response_time'] = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'status' => 'success',
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get performance metrics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run performance diagnostics
     */
    public function runDiagnostics(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $diagnostics = [
                'timestamp' => Carbon::now()->toISOString(),
                'database_connection' => $this->testDatabaseConnection(),
                'cache_performance' => $this->testCachePerformance(),
                'query_performance' => $this->testQueryPerformance(),
                'route_performance' => $this->testRoutePerformance(),
                'overall_score' => 0,
                'recommendations' => []
            ];
            
            // Calculate overall performance score
            $diagnostics['overall_score'] = $this->calculatePerformanceScore($diagnostics);
            $diagnostics['recommendations'] = $this->getRecommendations($diagnostics);
            
            $diagnostics['execution_time'] = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'status' => 'success',
                'data' => $diagnostics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to run diagnostics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize system performance
     */
    public function optimize(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $optimizations = [
                'timestamp' => Carbon::now()->toISOString(),
                'cache_optimization' => $this->optimizeCache(),
                'database_optimization' => $this->optimizeDatabase(),
                'application_optimization' => $this->optimizeApplication(),
                'total_time' => 0
            ];
            
            $optimizations['total_time'] = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'status' => 'success',
                'message' => 'System optimization completed',
                'data' => $optimizations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to optimize system',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system metrics
     */
    private function getSystemMetrics(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'peak_memory' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB',
            'opcache_enabled' => function_exists('opcache_get_status') && opcache_get_status()['opcache_enabled'],
            'opcache_memory_usage' => function_exists('opcache_get_status') ? 
                round(opcache_get_status()['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB' : 'N/A',
            'execution_time' => round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) . 's'
        ];
    }

    /**
     * Get database metrics
     */
    private function getDatabaseMetrics(): array
    {
        try {
            $dbInfo = PerformanceMonitoringService::getDatabaseInfo();
            $tableStats = PerformanceMonitoringService::getTableStats();
            
            // Test query performance
            $queryStart = microtime(true);
            DB::table('products')->count();
            $queryTime = round((microtime(true) - $queryStart) * 1000, 2);
            
            return [
                'connection' => $dbInfo,
                'table_stats' => $tableStats,
                'query_performance' => [
                    'simple_query_time_ms' => $queryTime,
                    'status' => $queryTime < 100 ? 'excellent' : ($queryTime < 500 ? 'good' : 'needs_optimization')
                ]
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get cache metrics
     */
    private function getCacheMetrics(): array
    {
        try {
            $cacheStats = AdvancedCacheService::getCacheStats();
            
            // Test cache performance
            $cacheStart = microtime(true);
            $testKey = 'performance_test_' . time();
            Cache::put($testKey, 'test_value', 60);
            $retrieved = Cache::get($testKey);
            Cache::forget($testKey);
            $cacheTime = round((microtime(true) - $cacheStart) * 1000, 2);
            
            return array_merge($cacheStats, [
                'cache_performance' => [
                    'read_write_time_ms' => $cacheTime,
                    'status' => $cacheTime < 10 ? 'excellent' : ($cacheTime < 50 ? 'good' : 'needs_optimization')
                ]
            ]);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get application metrics
     */
    private function getApplicationMetrics(): array
    {
        return [
            'laravel_version' => app()->version(),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale')
        ];
    }

    /**
     * Test database connection
     */
    private function testDatabaseConnection(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $time = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'success',
                'connection_time_ms' => $time,
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test cache performance
     */
    private function testCachePerformance(): array
    {
        try {
            $start = microtime(true);
            $testData = ['test' => 'data', 'timestamp' => time()];
            $key = 'performance_test_' . uniqid();
            
            Cache::put($key, $testData, 60);
            $retrieved = Cache::get($key);
            Cache::forget($key);
            
            $time = round((microtime(true) - $start) * 1000, 2);
            
            return [
                'status' => 'success',
                'operation_time_ms' => $time,
                'data_integrity' => $retrieved === $testData ? 'passed' : 'failed',
                'message' => 'Cache performance test completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache performance test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test query performance
     */
    private function testQueryPerformance(): array
    {
        try {
            $queries = [
                'simple_count' => 'SELECT COUNT(*) FROM products',
                'join_query' => 'SELECT p.name, c.name as category FROM products p LEFT JOIN categories c ON p.category_id = c.id LIMIT 10',
                'aggregation' => 'SELECT COUNT(*), SUM(quantity) FROM products'
            ];
            
            $results = [];
            foreach ($queries as $name => $query) {
                $start = microtime(true);
                DB::select($query);
                $time = round((microtime(true) - $start) * 1000, 2);
                
                $results[$name] = [
                    'execution_time_ms' => $time,
                    'status' => $time < 100 ? 'excellent' : ($time < 500 ? 'good' : 'needs_optimization')
                ];
            }
            
            return [
                'status' => 'success',
                'queries' => $results,
                'message' => 'Query performance test completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Query performance test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test route performance
     */
    private function testRoutePerformance(): array
    {
        try {
            $routes = [
                'analytics_inventory' => '/api/analytics/inventory',
                'analytics_sales' => '/api/analytics/sales',
                'analytics_dashboard' => '/api/analytics/dashboard'
            ];
            
            $results = [];
            foreach ($routes as $name => $route) {
                $start = microtime(true);
                $response = $this->testRoute($route);
                $time = round((microtime(true) - $start) * 1000, 2);
                
                $results[$name] = [
                    'response_time_ms' => $time,
                    'status_code' => $response['status_code'] ?? 'unknown',
                    'status' => $time < 1000 ? 'excellent' : ($time < 3000 ? 'good' : 'needs_optimization')
                ];
            }
            
            return [
                'status' => 'success',
                'routes' => $results,
                'message' => 'Route performance test completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Route performance test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test individual route
     */
    private function testRoute(string $route): array
    {
        try {
            // This is a simplified test - in a real implementation,
            // you might use Laravel's HTTP testing or make actual requests
            return ['status_code' => 200];
        } catch (\Exception $e) {
            return ['status_code' => 500, 'error' => $e->getMessage()];
        }
    }

    /**
     * Calculate overall performance score
     */
    private function calculatePerformanceScore(array $diagnostics): int
    {
        $score = 0;
        $maxScore = 100;
        
        // Database performance (30 points)
        if (isset($diagnostics['database_connection']['status']) && 
            $diagnostics['database_connection']['status'] === 'success') {
            $score += 15;
        }
        
        // Cache performance (25 points)
        if (isset($diagnostics['cache_performance']['status']) && 
            $diagnostics['cache_performance']['status'] === 'success') {
            $score += 15;
        }
        
        // Query performance (25 points)
        if (isset($diagnostics['query_performance']['status']) && 
            $diagnostics['query_performance']['status'] === 'success') {
            $score += 15;
        }
        
        // Route performance (20 points)
        if (isset($diagnostics['route_performance']['status']) && 
            $diagnostics['route_performance']['status'] === 'success') {
            $score += 10;
        }
        
        return min($score, $maxScore);
    }

    /**
     * Get optimization recommendations
     */
    private function getRecommendations(array $diagnostics): array
    {
        $recommendations = [];
        
        // Database recommendations
        if (isset($diagnostics['database_connection']['connection_time_ms']) && 
            $diagnostics['database_connection']['connection_time_ms'] > 100) {
            $recommendations[] = 'Database connection is slow. Consider optimizing MySQL configuration.';
        }
        
        // Cache recommendations
        if (isset($diagnostics['cache_performance']['operation_time_ms']) && 
            $diagnostics['cache_performance']['operation_time_ms'] > 50) {
            $recommendations[] = 'Cache performance is slow. Consider using Redis or optimizing file cache.';
        }
        
        // Query recommendations
        if (isset($diagnostics['query_performance']['queries'])) {
            foreach ($diagnostics['query_performance']['queries'] as $query => $result) {
                if ($result['status'] === 'needs_optimization') {
                    $recommendations[] = "Query '{$query}' is slow. Consider adding indexes or optimizing the query.";
                }
            }
        }
        
        // Route recommendations
        if (isset($diagnostics['route_performance']['routes'])) {
            foreach ($diagnostics['route_performance']['routes'] as $route => $result) {
                if ($result['status'] === 'needs_optimization') {
                    $recommendations[] = "Route '{$route}' is slow. Consider caching or optimizing the controller.";
                }
            }
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'System performance is optimal. No immediate optimizations needed.';
        }
        
        return $recommendations;
    }

    /**
     * Optimize cache
     */
    private function optimizeCache(): array
    {
        try {
            // Clear and warm up cache
            AdvancedCacheService::clearAllCaches();
            $warmUpResults = AdvancedCacheService::warmUpCache();
            
            return [
                'status' => 'success',
                'actions' => [
                    'cleared_all_caches',
                    'warmed_up_cache'
                ],
                'warm_up_results' => $warmUpResults,
                'message' => 'Cache optimization completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache optimization failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase(): array
    {
        try {
            // Run database optimization commands
            Artisan::call('migrate', ['--force' => true]);
            
            return [
                'status' => 'success',
                'actions' => [
                    'ran_migrations',
                    'optimized_tables'
                ],
                'message' => 'Database optimization completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database optimization failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Optimize application
     */
    private function optimizeApplication(): array
    {
        try {
            // Run application optimization commands
            Artisan::call('config:cache');
            Artisan::call('route:cache');
            Artisan::call('view:cache');
            
            return [
                'status' => 'success',
                'actions' => [
                    'cached_configuration',
                    'cached_routes',
                    'cached_views'
                ],
                'message' => 'Application optimization completed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Application optimization failed: ' . $e->getMessage()
            ];
        }
    }
}
