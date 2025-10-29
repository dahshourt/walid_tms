<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    use HasFactory;

    protected $table = 'statuses';

    protected $with = ['high_level'];

    protected $appends = ['name'];

    /**
     * The attributes that are mass assignable. test
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'status_name',
        'stage_id',
        'sla',
        'active',
        'view_technical_team_flag',
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function stage()
    {
        return $this->belongsTo(Stage::class);
    }

    public function group_statuses()
    {
        return $this->hasMany(GroupStatuses::class);
    }

    public function setByGroupStatuses(): HasMany
    {
        return $this->group_statuses()
            ->where('type', GroupStatuses::SETBY);
    }

    public function viewByGroupStatuses(): HasMany
    {
        return $this->group_statuses()
            ->where('type', GroupStatuses::VIEWBY);
    }

    public function from_status()
    {
        return $this->hasMany(StatusWorkFlow::class, 'from_status_id');
    }

    public function to_status()
    {
        return $this->hasMany(StatusWorkFlow::class, 'to_status_id');
    }

    public function to_status_workflow()
    {
        return $this->hasMany(Workflow::class, 'from_status_id');
    }

    public function high_level()
    {
        return $this->belongsTo(HighLevelStatuses::class, 'high_level_status_id')->where('active', '1');
    }

    public function getNameAttribute()
    {
        return $this->status_name;
    }
}
