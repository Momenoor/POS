<?php

namespace App\Models;

use App\Observers\JournalObserver;
use App\Observers\UserIdObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

#[ObservedBy([UserIdObserver::class, JournalObserver::class])]
class JournalEntry extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'entry_date', 'reference_number', 'memo', 'user_id',
        'referenceable_type', 'referenceable_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class);
    }

    public function referenceable(): MorphTo
    {
        return $this->morphTo();
    }

    public function totalDebit(): Attribute
    {
        return Attribute::get(fn() => $this->journalItems()->sum('debit'));;
    }

    public function totalCredit(): Attribute
    {
        return Attribute::get(fn() => $this->journalItems()->sum('credit'));;
    }

}
