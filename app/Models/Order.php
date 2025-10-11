<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Services\AdminNotificationService;
use App\Models\CustomerNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];

    protected $fillable = [
        'customer_id',
        'order_date',
        'order_status',
        'total_products',
        'sub_total',
        'vat',
        'total',
        'invoice_no',
        'tracking_number',
        'payment_type',
        'pay',
        'due',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
        'estimated_delivery',
        'delivery_notes',
        'delivery_address',
        'contact_phone',
        'gcash_reference',        
        'proof_of_payment',  
    ];

    protected $casts = [
        'order_date'    => 'date',
        'cancelled_at'  => 'datetime',
        'estimated_delivery' => 'datetime',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
        'order_status'  => OrderStatus::class,
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Create notification when a new order is created
        static::created(function ($order) {
            if ($order->order_status === OrderStatus::PENDING) {
                $notificationService = app(AdminNotificationService::class);
                $notificationService->createPendingOrderNotification($order);
            }
        });

        // Create customer notifications when order status changes
        static::updated(function ($order) {
            $originalStatus = $order->getOriginal('order_status');
            $newStatus = $order->order_status;

            // Helper function to normalize status values
            $normalizeStatus = function($status) {
                if ($status === 0 || $status === '0') return 'pending';
                if ($status === 1 || $status === '1') return 'complete';
                if ($status === 2 || $status === '2') return 'cancelled';
                return $status;
            };

            $originalStatusNormalized = $normalizeStatus($originalStatus);
            $newStatusNormalized = $normalizeStatus($newStatus);

            // If status changed from pending to complete (approved)
            if ($originalStatusNormalized === 'pending' && $newStatusNormalized === 'complete') {
                CustomerNotification::create([
                    'customer_id' => $order->customer_id,
                    'type' => 'order_approved',
                    'title' => 'Order Approved',
                    'message' => "Your order #{$order->invoice_no} has been approved and is being processed.",
                    'data' => [
                        'order_id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'status' => 'approved',
                    ],
                ]);
            }

            // If status changed to cancelled (admin cancelled)
            if ($originalStatusNormalized !== 'cancelled' && $newStatusNormalized === 'cancelled' && !is_null($order->cancelled_at)) {
                CustomerNotification::create([
                    'customer_id' => $order->customer_id,
                    'type' => 'order_cancelled',
                    'title' => 'Order Cancelled',
                    'message' => "Your order #{$order->invoice_no} has been cancelled by admin. Reason: {$order->cancellation_reason}",
                    'data' => [
                        'order_id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'status' => 'cancelled',
                        'cancellation_reason' => $order->cancellation_reason,
                    ],
                ]);
            }
        });
    }

    /**
     * Get the customer that owns the order
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the order details
     */
    public function details(): HasMany
    {
        return $this->hasMany(OrderDetails::class);
    }

    /**
     * Get the payments for this order
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the user who cancelled the order
     */
    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->order_status === OrderStatus::PENDING;
    }

    /**
     * Check if order is completed
     */
    public function isCompleted(): bool
    {
        return $this->order_status === OrderStatus::COMPLETE;
    }

    /**
     * Check if order is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->order_status === OrderStatus::CANCELLED;
    }

    /**
     * Check if order can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this->isPending() && !$this->isCancelled();
    }

    /**
     * Cancel the order
     */
    public function cancel(string $reason, ?User $cancelledBy = null): void
    {
        $this->update([
            'order_status' => OrderStatus::CANCELLED,
            'cancellation_reason' => $reason,
            'cancelled_at' => now(),
            'cancelled_by' => $cancelledBy?->id,
        ]);

        // Create admin notification for cancelled order
        $notificationService = app(AdminNotificationService::class);
        $notificationService->createCancelledOrderNotification($this, $cancelledBy);
    }

    /**
     * Get total paid amount
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->completed()->sum('amount');
    }

    /**
     * Get remaining balance
     */
    public function getRemainingBalanceAttribute(): float
    {
        return $this->total - $this->total_paid;
    }

    /**
     * Check if order is fully paid
     */
    public function isFullyPaid(): bool
    {
        return $this->remaining_balance <= 0;
    }

    /**
     * Search scope
     */
    public function scopeSearch($query, $value): void
    {
        $query->where('invoice_no', 'like', "%{$value}%")
            ->orWhere('tracking_number', 'like', "%{$value}%")
            ->orWhere('order_status', 'like', "%{$value}%")
            ->orWhere('payment_type', 'like', "%{$value}%");
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('order_status', OrderStatus::PENDING);
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('order_status', OrderStatus::COMPLETE);
    }

    /**
     * Scope for cancelled orders
     */
    public function scopeCancelled($query)
    {
        return $query->where('order_status', OrderStatus::CANCELLED);
    }
}
