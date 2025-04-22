<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Account extends Model implements AuditableContract
{
    use HasFactory, Auditable, LogsActivity;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_account_id',
        'is_system_account',
        'opening_balance',
        'description'
    ];

    protected $casts = [
        'is_system_account' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['code', 'name', 'type'])
            ->logOnlyDirty();
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_account_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class, 'parent_account_id');
    }

    public function journalItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    public function expenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function bankAccount(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BankAccount::class);
    }

    public function currentBalance(): Attribute
    {
        return Attribute::get(function () {
            $debit = $this->total_debit;
            $credit = $this->total_credit;
            return in_array($this->type, ['asset', 'expense'])
                ? $debit - $credit
                : $credit - $debit;
        });
    }

    public function depth(): Attribute
    {
        return Attribute::get(function () {
            $depth = 0;
            $parent = $this->parent;

            while ($parent) {
                $depth++;
                $parent = $parent->parent;
            }

            return $depth;
        });
    }

    public function scopeExpenses($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeAssets($query)
    {
        return $query->where('type', 'asset');
    }

    public function scopeLiabilities($query)
    {
        return $query->where('type', 'liability');
    }

    public function scopeEquity($query)
    {
        return $query->where('type', 'equity');
    }

    public function scopeRevenue($query)
    {
        return $query->where('type', 'revenue');
    }

}
