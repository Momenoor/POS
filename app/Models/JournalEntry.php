<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class JournalEntry extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = ['entry_date', 'reference_number', 'memo', 'created_by'];

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JournalItem::class);
    }
}
