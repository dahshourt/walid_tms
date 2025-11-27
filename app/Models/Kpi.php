<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kpi extends Model
{
    use HasFactory;

    protected $table = 'kpis';

    protected $fillable = [
        'name',
        'priority',
        'strategic_pillar',
        'initiative',
        'sub_initiative',
        'bu',
        'sub_bu',
        'target_launch_quarter',
        'target_launch_year',
        'type',
        'classification',
        'kpi_brief',
        'status',
        'created_by',
    ];
    
    const PRIORITY = ['Critical', 'High', 'Medium', 'Low'];
    const QUARTER = ['Q1', 'Q2', 'Q3', 'Q4'];
    const TYPE = ['Test Type 1', 'Test Type 2', 'Test Type 3', 'Test Type 4'];
    const CLASSIFICATION = ['CR', 'PM'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(KpiComment::class)->latest();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(KpiLog::class)->latest();
    }

    public function changeRequests(): BelongsToMany
    {
        return $this->belongsToMany(Change_request::class, 'kpi_change_request', 'kpi_id', 'cr_id');
    }
}
