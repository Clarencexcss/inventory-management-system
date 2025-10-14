<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staff';

    protected $fillable = [
        'name',
        'position',
        'department',
        'contact_number',
        'date_hired',
        'status'
    ];

    protected $casts = [
        'date_hired' => 'date',
    ];

    /**
     * Get all performance records for this staff member
     */
    public function performances()
    {
        return $this->hasMany(StaffPerformance::class);
    }

    /**
     * Get the average overall performance
     */
    public function getAveragePerformanceAttribute()
    {
        return $this->performances()->avg('overall_performance') ?? 0;
    }

    /**
     * Get the latest performance record
     */
    public function latestPerformance()
    {
        return $this->hasOne(StaffPerformance::class)->latestOfMany('month');
    }
}
