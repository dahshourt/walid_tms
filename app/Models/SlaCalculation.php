<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaCalculation extends Model
{
    use HasFactory;

    protected $table = 'sla_calculations';

    protected $fillable = [
        'unit_sla_time',
        'division_sla_time',
        'director_sla_time',
        'type',
        'status_id',
        'group_id',
    ];

    /**
     * Relationship with Status.
     */
    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * Relationship with Group.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
