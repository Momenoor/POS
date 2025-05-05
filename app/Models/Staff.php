<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Staff extends Model implements AuditableContract
{
    use HasFactory, Auditable;

    protected $fillable = [
        'user_id', 'restaurant_id', 'position', 'salary', 'passport_number',
        'passport_expiry_date', 'emirates_id_number', 'emirates_id_expiry_date',
        'nationality', 'phone', 'hire_date', 'termination_date', 'notes'
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'emirates_id_expiry_date' => 'date',
        'passport_expiry_date' => 'date',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function shifts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function orders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class);
    }
}
