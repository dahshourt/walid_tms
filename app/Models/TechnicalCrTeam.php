<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalCrTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id','technical_cr_id', 'status','current_status_id'
    ];
    const INACTIVE = '0';
    const APPROVED = '1';
    const REJECTED = '2';

    public static $statuses = [
        self::INACTIVE => 'Inactive',
        self::APPROVED => 'Approved',
        self::REJECTED => 'Rejected',
    ];

    public function isApproved ()
    {
        return $this->status == self::APPROVED;
    }
    public function isRejected ()
    {
        return $this->status == self::REJECTED;
    }

    public function isInactive ()
    {
        return $this->status == self::INACTIVE;
    }
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function technical_cr()
    {
        return $this->belongsTo(TechnicalCr::class);
    }
}
