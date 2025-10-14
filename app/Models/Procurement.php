<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'product_id',
        'quantity_supplied',
        'expected_delivery_date',
        'delivery_date',
        'total_cost',
        'status',
        'defective_rate',
    ];

    protected $casts = [
        'expected_delivery_date' => 'date',
        'delivery_date' => 'date',
        'total_cost' => 'decimal:2',
        'defective_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if delivery was on time
     */
    public function isOnTime(): bool
    {
        if (!$this->delivery_date || !$this->expected_delivery_date) {
            return false;
        }
        
        return $this->delivery_date <= $this->expected_delivery_date;
    }

    /**
     * Get delivery delay in days
     */
    public function getDeliveryDelayDays(): int
    {
        if (!$this->delivery_date || !$this->expected_delivery_date) {
            return 0;
        }
        
        $delay = $this->delivery_date->diffInDays($this->expected_delivery_date, false);
        return $delay < 0 ? abs($delay) : 0;
    }
}
