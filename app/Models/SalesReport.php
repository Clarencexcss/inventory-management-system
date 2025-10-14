<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'gross_sales',
        'total_expenses',
        'net_profit',
        'notes'
    ];

    protected $casts = [
        'gross_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    /**
     * Get the expense for this sales report
     */
    public function expense()
    {
        return $this->hasOne(MonthlyExpense::class, function($query) {
            $query->whereColumn('monthly_expenses.year', 'sales_reports.year')
                  ->whereColumn('monthly_expenses.month', 'sales_reports.month');
        });
    }

    /**
     * Get formatted month name
     */
    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    /**
     * Get formatted period
     */
    public function getPeriodAttribute()
    {
        return $this->month_name . ' ' . $this->year;
    }
}
