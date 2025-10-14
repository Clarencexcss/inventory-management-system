<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'total_sales',
        'total_expenses',
        'net_profit'
    ];

    protected $casts = [
        'total_sales' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
    ];

    /**
     * Get month name from number
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
