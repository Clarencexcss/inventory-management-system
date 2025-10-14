<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlySalesInput extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'month',
        'sales_amount',
        'user_id',
        'notes'
    ];

    protected $casts = [
        'sales_amount' => 'decimal:2',
    ];

    /**
     * Get the user who entered this data
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
