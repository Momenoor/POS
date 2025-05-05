<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Account extends Model implements AuditableContract
{
    use HasFactory, Auditable;

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
        'is_system_account' => 'boolean',
        'opening_balance' => 'decimal:2',
    ];

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

    public function scopeLeafs($query)
    {
        return $query->whereNotExists(function ($subquery) {
            $subquery->select(DB::raw(1))
                ->from('accounts as child')
                ->whereColumn('child.parent_id', 'accounts.id');
        });
    }

    public function scopeParents($query)
    {
        return $query->whereExists(function ($subquery) {
            $subquery->select(DB::raw(1))
                ->from('accounts as child')
                ->whereColumn('child.parent_id', 'accounts.id');
        });
    }
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_account_id');
    }

    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    public function formattedLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => '[' . $this->code . '] - ' . $this->name,
        );
    }

    public function getAllParents()
    {
        $parents = collect();
        $currentParent = $this->parent;

        while ($currentParent !== null) {
            $parents->push($currentParent);
            $currentParent = $currentParent->parent;
        }

        return $parents;
    }

    public function updateParentBalances(float $amountChange): void
    {
        // Early return if there's no change or this isn't a leaf account
        if ($amountChange == 0) {
            return;
        }

        // Get all parent accounts in hierarchy
        $parents = $this->getAllParents();

        // Update each parent's current_balance
        foreach ($parents as $parent) {
            // Use DB::raw to ensure atomicity when updating
            $parent->increment('current_balance', $amountChange);
        }
    }

    public static function recalculateAllParentBalances(): void
    {
        // First, reset all parent account balances to opening_balance
        self::parents()->update([
            'current_balance' => DB::raw('opening_balance')
        ]);

        // Group all leaf account balances by parent_id
        $leafAccounts = self::leafs()->get();

        // Process each leaf account
        foreach ($leafAccounts as $leafAccount) {
            if ($leafAccount->parent_id) {
                $leafAccount->updateParentBalances($leafAccount->current_balance - $leafAccount->opening_balance);
            }
        }
    }

}
