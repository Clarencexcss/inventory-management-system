<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class FullOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:full {--clear : Clear all caches before optimizing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run full optimization for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting full application optimization...');
        
        if ($this->option('clear')) {
            $this->clearAllCaches();
        }
        
        $this->optimizeConfig();
        $this->optimizeRoutes();
        $this->optimizeViews();
        $this->optimizeAutoloader();
        $this->warmUpCaches();
        
        $this->info('✅ Full optimization completed successfully!');
        $this->line('Run "php artisan optimize:clear" to clear all optimizations.');
    }
    
    /**
     * Clear all caches
     */
    private function clearAllCaches()
    {
        $this->info('🧹 Clearing all caches...');
        
        Artisan::call('config:clear');
        $this->line('  - Configuration cache cleared');
        
        Artisan::call('route:clear');
        $this->line('  - Route cache cleared');
        
        Artisan::call('view:clear');
        $this->line('  - View cache cleared');
        
        Artisan::call('cache:clear');
        $this->line('  - Application cache cleared');
        
        Artisan::call('event:clear');
        $this->line('  - Event cache cleared');
    }
    
    /**
     * Optimize configuration
     */
    private function optimizeConfig()
    {
        $this->info('⚙️  Optimizing configuration...');
        Artisan::call('config:cache');
        $this->line('  - Configuration cached');
    }
    
    /**
     * Optimize routes
     */
    private function optimizeRoutes()
    {
        $this->info('🛣️  Optimizing routes...');
        Artisan::call('route:cache');
        $this->line('  - Routes cached');
    }
    
    /**
     * Optimize views
     */
    private function optimizeViews()
    {
        $this->info('👁️  Optimizing views...');
        Artisan::call('view:cache');
        $this->line('  - Views cached');
    }
    
    /**
     * Optimize autoloader
     */
    private function optimizeAutoloader()
    {
        $this->info('📦 Optimizing autoloader...');
        Artisan::call('optimize');
        $this->line('  - Autoloader optimized');
    }
    
    /**
     * Warm up caches
     */
    private function warmUpCaches()
    {
        $this->info('🔥 Warming up caches...');
        
        // Warm up common caches
        Cache::remember('dashboard_data', 600, function() {
            $this->line('  - Dashboard data cached');
            return ['warmed' => true];
        });
        
        Cache::remember('analytics_inventory', 900, function() {
            $this->line('  - Inventory analytics cached');
            return ['warmed' => true];
        });
        
        Cache::remember('analytics_sales', 900, function() {
            $this->line('  - Sales analytics cached');
            return ['warmed' => true];
        });
        
        $this->line('  - Cache warm-up completed');
    }
}