<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'electricity_bill',
        'staff_salaries',
        'product_resupply',
        'equipment_maintenance',
        'total'
    ];

    protected $casts = [
        'electricity_bill' => 'decimal:2',
        'staff_salaries' => 'decimal:2',
        'product_resupply' => 'decimal:2',
        'equipment_maintenance' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Boot method to calculate total
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($expense) {
            $expense->total = $expense->electricity_bill + 
                            $expense->staff_salaries + 
                            $expense->product_resupply + 
                            $expense->equipment_maintenance;
        });
    }

    /**
     * Get formatted month name
     */
    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }
}
