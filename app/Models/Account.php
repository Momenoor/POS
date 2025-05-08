<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Kalnoy\Nestedset\NodeTrait;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Account extends Model implements AuditableContract
{
    use HasFactory, Auditable, NodeTrait;

    protected $fillable = [
        'code',
        'name',
        'type',
        'parent_id',
        '_lft',
        '_rgt',
        'is_system_account',
        'opening_balance',
        'description'
    ];

    protected $casts = [
        'is_system_account' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

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

    public function formattedLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => '[' . $this->code . '] - ' . $this->name,
        );
    }

    public function debitOpeningBalance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->opening_balance >= 0 ? $this->opening_balance : 0,
        );
    }

    public function creditOpeningBalance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->opening_balance < 0 ? $this->opening_balance * -1 : 0,
        );
    }

    public function debitCurrentBalance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->current_balance >= 0 ? $this->current_balance : 0,
        );
    }

    public function creditCurrentBalance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->current_balance < 0 ? $this->current_balance * -1 : 0,
        );
    }

//    public function finalOpeningBalance(): Attribute
//    {
//        return Attribute::make(
//            get: function() {
//                if($this->isLeaf())
//                {
//                    return $this->opening_balance;
//                }
//                else{
//                    return $this->children->map(function($child){
//                        return $child->final_opening_balance;
//                    })->sum();
//                }
//            }
//        );
//    }
//
//    public function finalCurrentBalance(): Attribute
//    {
//        $this->load(['children']);
//        return Attribute::make(
//            get: function() {
//                if($this->isLeaf())
//                {
//                    return $this->current_balance;
//                }
//                else{
//                    return $this->children->map(function($child){
//                        return $child->final_current_balance;
//                    })->sum();
//                }
//            }
//        );
//    }

}
