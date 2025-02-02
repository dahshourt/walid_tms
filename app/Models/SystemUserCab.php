<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemUserCab extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'system_id','active'
    ];
    const ACTIVE = '1';
    const INACTIVE = '0';

    public static $actives = [
        self::ACTIVE => 'Active',
        self::INACTIVE => 'Inactive',
    ];

    public function isActive ()
    {
        return $this->active == self::ACTIVE;
    }

    public function isInactive ()
    {
        return $this->active == self::INACTIVE;
    }
    
    public function system()
    {
        return $this->belongsTo(Application::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}
