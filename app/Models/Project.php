<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'project_manager_name',
    ];

    const STATUS = [
        'Not Started',
        'In Progress',
        'Delivered',
        'On-Hold',
        'Canceled',
    ];

    /**
     * Get the quarters for the project.
     */
    public function quarters(): HasMany
    {
        return $this->hasMany(ProjectKpiQuarter::class);
    }
}
