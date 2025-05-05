<?php

namespace App\Models;

use App\Enums\OrderStatusEnum;
use App\Enums\PaymentMethodEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Order extends Model implements AuditableContract
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'table_id', 'user_id', 'customer_id', 'staff_id', 'status',
        'subtotal', 'tax_amount', 'discount_amount', 'total',
        'payment_id', 'notes'
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($order) {
            $order->calculateTotals();
        });

        static::updating(function ($order) {
            $order->calculateTotals();
        });
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
    public function calculateTotals(): void
    {
        $subtotal = $this->orderItems()->sum('subtotal');
        $this->subtotal = (float)$subtotal;
        $this->total = (float)$subtotal + (float)$this->tax_amount - (float)$this->discount_amount;
    }

    public function orderItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function staff(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', OrderStatusEnum::getFailedStatuses());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNull('payment_method');
    }

    public function scopePaid($query)
    {
        return $query->whereNotNull('payment_method');
    }
}
