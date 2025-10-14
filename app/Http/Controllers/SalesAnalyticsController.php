<?php

namespace App\Http\Controllers;

use App\Models\SalesRecord;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use Barryvdh\DomPDF\Facade\Pdf;

class SalesAnalyticsController extends Controller
{
    /**
     * Display the sales analytics dashboard
     */
    public function index()
    {
        // Get 2020-2024 data (preloaded)
        $historicalData = SalesRecord::where('year', '<', 2025)
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Get yearly summary for 2020-2024
        $yearlySummary = SalesRecord::where('year', '<', 2025)
            ->select(
                'year',
                DB::raw('SUM(total_sales) as total_sales'),
                DB::raw('SUM(total_expenses) as total_expenses'),
                DB::raw('SUM(net_profit) as net_profit')
            )
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        // Get top-selling products
        $topProducts = $this->getTopSellingProducts();

        // Calculate insights
        $insights = [
            'highest_sales_year' => $yearlySummary->sortByDesc('total_sales')->first(),
            'most_profitable_year' => $yearlySummary->sortByDesc('net_profit')->first(),
            'top_product' => $topProducts->first()
        ];

        // Get all categories for filter
        $categories = Category::all();

        return view('reports.sales-analytics', compact(
            'historicalData',
            'yearlySummary',
            'topProducts',
            'insights',
            'categories'
        ));
    }

    /**
     * Get 2025 data via AJAX
     */
    public function get2025Data()
    {
        $data2025 = SalesRecord::where('year', 2025)
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data2025
        ]);
    }

    /**
     * Store or update 2025 monthly record
     */
    public function store2025(Request $request)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'total_sales' => 'required|numeric|min:0',
            'total_expenses' => 'required|numeric|min:0'
        ]);

        $netProfit = $validated['total_sales'] - $validated['total_expenses'];

        $record = SalesRecord::updateOrCreate(
            [
                'year' => 2025,
                'month' => $validated['month']
            ],
            [
                'total_sales' => $validated['total_sales'],
                'total_expenses' => $validated['total_expenses'],
                'net_profit' => $netProfit
            ]
        );

        return response()->json([
            'success' => true,
            'message' => '✅ Sales record saved successfully!',
            'data' => $record
        ]);
    }

    /**
     * Get top-selling products
     */
    private function getTopSellingProducts($limit = 10)
    {
        return OrderDetails::select(
            'order_details.product_id',
            DB::raw('SUM(order_details.quantity) as total_quantity'),
            DB::raw('SUM(order_details.total) as total_revenue'),
            
            /* ✅ Fix: Use existing column names safely */
            DB::raw('SUM(order_details.quantity * (order_details.total / NULLIF(order_details.quantity, 0) - IFNULL(order_details.unitcost, 0))) as total_profit')
        )
        ->with(['product.category'])
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereIn('orders.order_status', [1, '1', 'complete']) // Completed orders
        ->groupBy('order_details.product_id')
        ->orderByDesc('total_revenue')
        ->limit($limit)
        ->get()
        ->map(function ($item) {
            $product = $item->product;
            if (!$product) return null;

            $profitMargin = $item->total_revenue > 0
                ? ($item->total_profit / $item->total_revenue) * 100
                : 0;

            return (object) [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'category_name' => $product->category->name ?? 'N/A',
                'total_quantity' => $item->total_quantity,
                'total_revenue' => $item->total_revenue,
                'total_profit' => $item->total_profit,
                'profit_margin' => round($profitMargin, 2)
            ];
        })->filter()->values();
    }

    /**
     * Export as PDF
     */
    public function exportPDF()
    {
        // Temporarily disabled until PDF library is installed
        return response()->json([
            'error' => 'PDF export is currently being configured. Please try CSV export instead.'
        ], 503);
        
        /* Uncomment when barryvdh/laravel-dompdf is installed
        $yearlySummary = SalesRecord::select(
            'year',
            DB::raw('SUM(total_sales) as total_sales'),
            DB::raw('SUM(total_expenses) as total_expenses'),
            DB::raw('SUM(net_profit) as net_profit')
        )
        ->groupBy('year')
        ->orderBy('year')
        ->get();

        $topProducts = $this->getTopSellingProducts(10);

        $pdf = Pdf::loadView('reports.sales-analytics-pdf', compact('yearlySummary', 'topProducts'));
        
        return $pdf->download('sales-analytics-' . date('Y-m-d') . '.pdf');
        */
    }

    /**
     * Export as CSV
     */
    public function exportCSV()
    {
        $records = SalesRecord::orderBy('year')->orderBy('month')->get();

        $filename = 'sales-analytics-' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, ['Year', 'Month', 'Total Sales', 'Total Expenses', 'Net Profit']);
            
            // Add data
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->year,
                    $record->month_name,
                    number_format($record->total_sales, 2),
                    number_format($record->total_expenses, 2),
                    number_format($record->net_profit, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
