<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shift extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'staff_id', 'start_time', 'end_time', 'total_earnings', 'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'total_earnings' => 'decimal:2',
    ];
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
