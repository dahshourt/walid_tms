<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeRequestHold extends Model
{
    protected $fillable = [
        'change_request_id',
        'hold_reason_id',
        'resuming_date',
        'justification',
    ];

    protected $casts = [
        'resuming_date' => 'date',
    ];

    public function changeRequest(): BelongsTo
    {
        return $this->belongsTo(Change_request::class);
    }

    public function holdReason(): BelongsTo
    {
        return $this->belongsTo(HoldReason::class);
    }
}
