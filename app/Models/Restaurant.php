<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Restaurant extends Model implements AuditableContract
{
    use HasFactory, Auditable, LogsActivity;

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id',
        'phone',
        'email',
        'website',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'timezone',
        'currency',
        'logo_path',
        'business_hours',
        'default_tax_rate_id',
        'is_active'
    ];

    protected $casts = [
        'business_hours' => 'array',
        'is_active' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'tax_id', 'is_active'])
            ->logOnlyDirty();
    }

    public function taxRate(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TaxRate::class, 'default_tax_rate_id');
    }

    public function staff(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function menuCategories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuCategory::class);
    }

    public function tables(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Table::class);
    }
}
