<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\StaffPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StaffPerformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $performances = StaffPerformance::with('staff')
            ->orderBy('month', 'desc')
            ->orderBy('overall_performance', 'desc')
            ->paginate(20);

        return view('admin.performance.index', compact('performances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $staff = Staff::where('status', 'Active')->orderBy('name')->get();
        $currentMonth = now()->format('Y-m');
        
        return view('admin.performance.create', compact('staff', 'currentMonth'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'month' => 'required|date_format:Y-m',
            'attendance_rate' => 'required|numeric|min:0|max:100',
            'task_completion_rate' => 'required|numeric|min:0|max:100',
            'customer_feedback_score' => 'required|numeric|min:1|max:5',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Check if performance already exists for this staff and month
        $exists = StaffPerformance::where('staff_id', $validated['staff_id'])
            ->where('month', $validated['month'])
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Performance record already exists for this staff member and month!');
        }

        StaffPerformance::create($validated);

        return redirect()
            ->route('staff-performance.index')
            ->with('success', 'Performance record has been created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(StaffPerformance $staffPerformance)
    {
        $staffPerformance->load('staff');
        
        return view('admin.performance.show', compact('staffPerformance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StaffPerformance $staffPerformance)
    {
        $staff = Staff::where('status', 'Active')->orderBy('name')->get();
        
        return view('admin.performance.edit', compact('staffPerformance', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StaffPerformance $staffPerformance)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'month' => 'required|date_format:Y-m',
            'attendance_rate' => 'required|numeric|min:0|max:100',
            'task_completion_rate' => 'required|numeric|min:0|max:100',
            'customer_feedback_score' => 'required|numeric|min:1|max:5',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Check if changing to a month/staff combo that already exists
        $exists = StaffPerformance::where('staff_id', $validated['staff_id'])
            ->where('month', $validated['month'])
            ->where('id', '!=', $staffPerformance->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Performance record already exists for this staff member and month!');
        }

        $staffPerformance->update($validated);

        return redirect()
            ->route('staff-performance.index')
            ->with('success', 'Performance record has been updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StaffPerformance $staffPerformance)
    {
        $staffPerformance->delete();

        return redirect()
            ->route('staff-performance.index')
            ->with('success', 'Performance record has been deleted successfully!');
    }

    /**
     * Display staff performance report
     */
    public function report()
    {
        try {
            // Get top 3 performers
            $topPerformers = StaffPerformance::with('staff')
                ->select('staff_id', DB::raw('AVG(overall_performance) as avg_performance'))
                ->whereHas('staff') // Ensure staff exists
                ->groupBy('staff_id')
                ->orderBy('avg_performance', 'desc')
                ->limit(3)
                ->get();

            // Get bottom 3 performers
            $bottomPerformers = StaffPerformance::with('staff')
                ->select('staff_id', DB::raw('AVG(overall_performance) as avg_performance'))
                ->whereHas('staff') // Ensure staff exists
                ->groupBy('staff_id')
                ->orderBy('avg_performance', 'asc')
                ->limit(3)
                ->get();

            // Get average performance by staff
            $staffAverages = StaffPerformance::with('staff')
                ->select('staff_id', DB::raw('AVG(overall_performance) as avg_performance'))
                ->whereHas('staff') // Ensure staff exists
                ->groupBy('staff_id')
                ->orderBy('avg_performance', 'desc')
                ->get();

            // Get monthly trend data (last 6 months)
            $monthlyTrends = StaffPerformance::select(
                    'month',
                    DB::raw('AVG(attendance_rate) as avg_attendance'),
                    DB::raw('AVG(task_completion_rate) as avg_task_completion'),
                    DB::raw('AVG(customer_feedback_score) as avg_feedback'),
                    DB::raw('AVG(overall_performance) as avg_performance')
                )
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->limit(6)
                ->get();
                
            // Process the monthly trends to ensure proper formatting
            $processedMonthlyTrends = $monthlyTrends->map(function ($item) {
                // Ensure month is properly formatted
                if ($item->month) {
                    $item->formatted_month = \Carbon\Carbon::parse($item->month)->format('M Y');
                } else {
                    $item->formatted_month = 'Unknown';
                }
                
                // Ensure all values are properly formatted
                $item->avg_performance = round($item->avg_performance, 2);
                $item->avg_attendance = round($item->avg_attendance, 2);
                $item->avg_task_completion = round($item->avg_task_completion, 2);
                $item->avg_feedback = round($item->avg_feedback, 2);
                
                return $item;
            })->reverse();

            return view('admin.performance.report', compact(
                'topPerformers',
                'bottomPerformers',
                'staffAverages',
                'processedMonthlyTrends'
            ));
        } catch (\Exception $e) {
            \Log::error('Staff Performance Report Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }
}
