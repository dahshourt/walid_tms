<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'manager_name',
        'status',
    ];


     public function group()
    {
        return $this->belongsTo(Group::class, 'id');
    }

    /**
     * Relationship with SLA Calculations.
     * (Each unit may have many SLA rules)
     */
    public function slaCalculations()
    {
        return $this->hasMany(SlaCalculation::class, 'unit_id');
    }
}
