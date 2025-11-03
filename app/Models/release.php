<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Release extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'go_live_planned_date',
        'planned_start_iot_date',
        'planned_end_iot_date',
        'planned_start_e2e_date',
        'planned_end_e2e_date',
        'planned_start_uat_date',
        'planned_end_uat_date',
        'planned_start_smoke_test_date',
        'planned_end_smoke_test_date',
        'release_status',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'release_status');
    }

    public function releaseStatus()
    {
        return $this->belongsTo(Release_statuse::class, 'release_status', 'id');
    }

    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class);
    }
}
