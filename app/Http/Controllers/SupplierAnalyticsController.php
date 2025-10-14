<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Procurement;
use App\Models\Purchase;
use App\Models\PurchaseDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SupplierAnalyticsController extends Controller
{
    /**
     * Display the Supplier Analytics dashboard
     */
    public function index()
    {
        $suppliers = Supplier::withCount(['procurements', 'purchases'])->get();
        
        $performanceData = $this->getSupplierPerformance();
        $deliveryTracking = $this->getDeliveryTracking();
        $procurementInsights = $this->getProcurementInsights();
        $topSuppliers = $this->getTopSuppliers();
        $monthlyTrends = $this->getMonthlyProcurementTrends();
        
        return view('reports.supplier-analytics', compact(
            'suppliers',
            'performanceData',
            'deliveryTracking',
            'procurementInsights',
            'topSuppliers',
            'monthlyTrends'
        ));
    }

    /**
     * Get supplier performance metrics
     */
    private function getSupplierPerformance()
    {
        $suppliers = Supplier::with(['procurements'])->get();
        
        $performance = [];
        
        foreach ($suppliers as $supplier) {
            $procurements = $supplier->procurements;
            
            if ($procurements->isEmpty()) {
                continue;
            }
            
            $totalDeliveries = $procurements->count();
            $onTimeDeliveries = $procurements->where('status', 'on-time')->count();
            $onTimeRate = $totalDeliveries > 0 ? ($onTimeDeliveries / $totalDeliveries) * 100 : 0;
            
            $avgDefectiveRate = $procurements->avg('defective_rate') ?? 0;
            $totalCost = $procurements->sum('total_cost');
            
            // Calculate average delivery delay for delayed deliveries
            $delayedDeliveries = $procurements->filter(function ($procurement) {
                return $procurement->delivery_date && 
                       $procurement->expected_delivery_date && 
                       $procurement->delivery_date > $procurement->expected_delivery_date;
            });
            
            $avgDelayDays = 0;
            if ($delayedDeliveries->count() > 0) {
                $totalDelayDays = $delayedDeliveries->sum(function ($procurement) {
                    return $procurement->delivery_date->diffInDays($procurement->expected_delivery_date);
                });
                $avgDelayDays = round($totalDelayDays / $delayedDeliveries->count(), 1);
            }
            
            $performance[] = [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->name,
                'total_deliveries' => $totalDeliveries,
                'on_time_deliveries' => $onTimeDeliveries,
                'on_time_rate' => round($onTimeRate, 2),
                'avg_defective_rate' => round($avgDefectiveRate, 2),
                'total_cost' => $totalCost,
                'avg_delay_days' => $avgDelayDays,
                'performance_score' => $this->calculatePerformanceScore($onTimeRate, $avgDefectiveRate),
            ];
        }
        
        return collect($performance)->sortByDesc('performance_score')->values();
    }

    /**
     * Calculate supplier performance score (0-100)
     */
    private function calculatePerformanceScore($onTimeRate, $defectiveRate)
    {
        // 70% weight on on-time delivery, 30% weight on quality (low defective rate)
        $onTimeScore = $onTimeRate * 0.7;
        $qualityScore = (100 - ($defectiveRate * 20)) * 0.3; // Penalize defective rate
        
        return round(max(0, min(100, $onTimeScore + $qualityScore)), 2);
    }

    /**
     * Get delivery tracking data
     */
    private function getDeliveryTracking()
    {
        $onTime = Procurement::where('status', 'on-time')->count();
        $delayed = Procurement::where('status', 'delayed')->count();
        $total = $onTime + $delayed;
        
        $onTimePercentage = $total > 0 ? round(($onTime / $total) * 100, 2) : 0;
        $delayedPercentage = $total > 0 ? round(($delayed / $total) * 100, 2) : 0;
        
        return [
            'on_time' => $onTime,
            'delayed' => $delayed,
            'total' => $total,
            'on_time_percentage' => $onTimePercentage,
            'delayed_percentage' => $delayedPercentage,
        ];
    }

    /**
     * Get procurement insights
     */
    private function getProcurementInsights()
    {
        $totalProcurementCost = Procurement::sum('total_cost');
        $totalQuantitySupplied = Procurement::sum('quantity_supplied');
        $avgCostPerProcurement = Procurement::avg('total_cost');
        $totalProcurements = Procurement::count();
        
        // Get recent procurement trend (last 30 days vs previous 30 days)
        $last30Days = Procurement::where('delivery_date', '>=', Carbon::now()->subDays(30))->sum('total_cost');
        $previous30Days = Procurement::whereBetween('delivery_date', [
            Carbon::now()->subDays(60),
            Carbon::now()->subDays(30)
        ])->sum('total_cost');
        
        $trendPercentage = $previous30Days > 0 
            ? round((($last30Days - $previous30Days) / $previous30Days) * 100, 2)
            : 0;
        
        return [
            'total_cost' => $totalProcurementCost,
            'total_quantity' => $totalQuantitySupplied,
            'avg_cost' => round($avgCostPerProcurement, 2),
            'total_procurements' => $totalProcurements,
            'trend_percentage' => $trendPercentage,
            'trend_direction' => $trendPercentage >= 0 ? 'up' : 'down',
        ];
    }

    /**
     * Get top suppliers by total cost
     */
    private function getTopSuppliers($limit = 5)
    {
        return Supplier::select('suppliers.id', 'suppliers.name')
            ->join('procurements', 'suppliers.id', '=', 'procurements.supplier_id')
            ->groupBy('suppliers.id', 'suppliers.name')
            ->selectRaw('SUM(procurements.total_cost) as total_spent')
            ->selectRaw('COUNT(procurements.id) as total_procurements')
            ->selectRaw('AVG(procurements.defective_rate) as avg_defect_rate')
            ->orderByDesc('total_spent')
            ->limit($limit)
            ->get()
            ->map(function ($supplier) {
                $supplier->avg_defect_rate = round($supplier->avg_defect_rate, 2);
                return $supplier;
            });
    }

    /**
     * Get monthly procurement cost trends (last 12 months)
     */
    private function getMonthlyProcurementTrends()
    {
        $trends = Procurement::select(
                DB::raw('DATE_FORMAT(delivery_date, "%Y-%m") as month'),
                DB::raw('SUM(total_cost) as total_cost'),
                DB::raw('COUNT(*) as procurement_count')
            )
            ->where('delivery_date', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return $trends->map(function ($trend) {
            return [
                'month' => Carbon::parse($trend->month . '-01')->format('M Y'),
                'total_cost' => $trend->total_cost,
                'procurement_count' => $trend->procurement_count,
            ];
        });
    }

    /**
     * Export supplier analytics data (Optional future enhancement)
     */
    public function export()
    {
        // TODO: Implement CSV/PDF export
        return redirect()->back()->with('info', 'Export feature coming soon!');
    }
}
