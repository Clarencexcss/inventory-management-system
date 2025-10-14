<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUpdateLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'staff_id',
        'user_id',
        'action',
        'changes'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    /**
     * Get the product associated with this log
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the staff member who made the update
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    /**
     * Get the user who made the update
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
