<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Shift extends Model implements AuditableContract
{
    use Auditable, SoftDeletes;

    protected $fillable = [
        'staff_id', 'start_time', 'end_time', 'total_earnings', 'notes'
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
