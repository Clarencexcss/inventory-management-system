<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Product extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'slug',
        'code',
        'category_id',
        'unit_id',
        'meat_cut_id',
        'quantity',
        'price_per_kg',
        'selling_price',
        'storage_location',
        'expiration_date',
        'source',
        'notes',
        'buying_price',
        'quantity_alert',
        'product_image',
        'updated_by' // Track who last updated the product
    ];

    public $sortable = [
        'name',
        'code',
        'quantity',
        'price_per_kg',
        'selling_price',
        'expiration_date'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'price_per_kg' => 'decimal:2'
    ];

    public function scopeSearch($query, $value)
    {
        return $query->where('name', 'like', "%{$value}%")
            ->orWhere('code', 'like', "%{$value}%")
            ->orWhereHas('meatCut', function($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
    }

    protected static function booted()
    {
        static::created(function ($product) {
            if ($product->quantity > 0) {
                \App\Models\InventoryMovement::create([
                    'product_id' => $product->id,
                    'type' => 'in',
                    'quantity' => $product->quantity,
                ]);
            }
        });
    }
    // Note: meatCut relationship removed as products table doesn't have meat_cut_id field
    // Use separate meat_cuts table for meat-specific products

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function getCurrentStockAttribute()
{
    $movements = $this->relationLoaded('inventoryMovements')
        ? $this->inventoryMovements
        : $this->inventoryMovements()->get();

    $in = $movements->where('type', 'in')->sum('quantity');
    $out = $movements->where('type', 'out')->sum('quantity');

    return $in - $out;
}

    public function meatCut()
    {
        return $this->belongsTo(MeatCut::class);
    }

    /**
     * Get the staff member who last updated this product
     */
    public function updatedByStaff()
    {
        return $this->belongsTo(Staff::class, 'updated_by');
    }

    /**
     * Get all update logs for this product
     */
    public function updateLogs()
    {
        return $this->hasMany(ProductUpdateLog::class);
    }

    /**
     * Get the latest update log
     */
    public function latestUpdateLog()
    {
        return $this->hasOne(ProductUpdateLog::class)->latestOfMany();
    }
} 