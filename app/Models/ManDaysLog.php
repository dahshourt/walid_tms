<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManDaysLog extends Model
{
    use HasFactory;

    protected $table = 'man_days_logs';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'group_id',
        'cr_id',
        'man_day',
    ];

    /**
     * Relationship to Group
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /**
     * Relationship to ChangeRequest
     */
    public function changeRequest()
    {
        return $this->belongsTo(ChangeRequest::class, 'cr_id');
    }
}
