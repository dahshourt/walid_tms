<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicalCr extends Model
{
    use HasFactory;
    protected $fillable = [
        'cr_id', 'status'
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

    public function change_request()
    {
        return $this->belongsTo(Change_request::class,'cr_id');
    }

    public function technical_cr_team()
    {
        return $this->hasMany(TechnicalCrTeam::class, 'technical_cr_id', 'id');
    }
    
}
