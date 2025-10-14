<?php

namespace App\Models;

use App\Enums\SupplierType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'shopname',
        'type',
        'status',
        'photo',
        'account_holder',
        'account_number',
        'bank_name',
        'contact_person',
        'delivery_rating',
        'average_lead_time',
        'total_procurements',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type' => SupplierType::class,
        'delivery_rating' => 'decimal:2',
        'average_lead_time' => 'integer',
        'total_procurements' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function procurements(): HasMany
    {
        return $this->hasMany(Procurement::class);
    }

    public function scopeSearch($query, $value): void
    {
        $query->where('name', 'like', "%{$value}%")
            ->orWhere('email', 'like', "%{$value}%")
            ->orWhere('phone', 'like', "%{$value}%")
            ->orWhere('shopname', 'like', "%{$value}%")
            ->orWhere('type', 'like', "%{$value}%")
            ->orWhere('status', 'like', "%{$value}%");
    }

    public function getTypeNameAttribute(): string
    {
        return $this->type->value ?? '';
    }

    /**
     * Update supplier analytics based on procurements
     */
    public function updateAnalytics(): void
    {
        $procurements = $this->procurements;
        
        if ($procurements->isEmpty()) {
            return;
        }

        // Update total procurements count
        $this->total_procurements = $procurements->count();

        // Calculate average lead time (delivery delay)
        $totalDelay = 0;
        $countWithDates = 0;
        
        foreach ($procurements as $procurement) {
            if ($procurement->delivery_date && $procurement->expected_delivery_date) {
                $delay = $procurement->delivery_date->diffInDays($procurement->expected_delivery_date, false);
                $totalDelay += abs($delay);
                $countWithDates++;
            }
        }
        
        $this->average_lead_time = $countWithDates > 0 ? round($totalDelay / $countWithDates) : 0;

        // Calculate delivery rating (0-5 scale based on on-time percentage)
        $onTimeCount = $procurements->where('status', 'on-time')->count();
        $totalCount = $procurements->count();
        $onTimePercentage = $totalCount > 0 ? ($onTimeCount / $totalCount) * 100 : 0;
        
        // Convert percentage to 0-5 scale
        $this->delivery_rating = round(($onTimePercentage / 100) * 5, 2);

        $this->save();
    }

    /**
     * Get on-time delivery percentage
     */
    public function getOnTimePercentageAttribute(): float
    {
        if ($this->delivery_rating > 0) {
            return round(($this->delivery_rating / 5) * 100, 2);
        }
        return 0.00;
    }
}
