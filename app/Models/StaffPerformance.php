<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPerformance extends Model
{
    use HasFactory;

    protected $table = 'staff_performance';

    protected $fillable = [
        'staff_id',
        'month',
        'attendance_rate',
        'task_completion_rate',
        'customer_feedback_score',
        'overall_performance',
        'remarks'
    ];

    protected $casts = [
        'attendance_rate' => 'float',
        'task_completion_rate' => 'float',
        'customer_feedback_score' => 'float',
        'overall_performance' => 'float',
    ];

    /**
     * Boot method to auto-calculate overall performance
     */
    protected static function booted()
    {
        static::saving(function ($performance) {
            // Calculate overall performance score
            // Formula: (Attendance * 30%) + (Task Completion * 40%) + (Feedback * 30%)
            $attendanceScore = ($performance->attendance_rate / 100) * 0.3;
            $taskScore = ($performance->task_completion_rate / 100) * 0.4;
            $feedbackScore = ($performance->customer_feedback_score / 5) * 0.3;
            
            $performance->overall_performance = round(
                ($attendanceScore + $taskScore + $feedbackScore) * 100,
                2
            );
        });
    }

    /**
     * Get the staff member for this performance record
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get performance grade based on overall score
     */
    public function getGradeAttribute()
    {
        $score = $this->overall_performance;
        
        if ($score >= 90) return 'Excellent';
        if ($score >= 80) return 'Very Good';
        if ($score >= 70) return 'Good';
        if ($score >= 60) return 'Satisfactory';
        return 'Needs Improvement';
    }
}
