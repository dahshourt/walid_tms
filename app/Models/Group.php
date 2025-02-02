<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'active',
        'parent_id',
        'head_group_name',
        'head_group_email',
        'man_power',
		'active'
    ];
    protected $appends = array('name');

    public function user_groups()
    {
        return $this->hasMany(UserGroups::class);
    }

    public function group_statuses()
    {
        return $this->hasMany(GroupStatuses::class);
    }
    
    public function group_applications()
    {
        return $this->hasMany(GroupApplications::class);
    }


    public function children() 
    {
        return $this->hasMany(Group::class,'parent_id');
    }
    public function parent() 
    {
        return $this->belongsTo(Group::class,'parent_id');
    }

    
    public function getNameAttribute()
    {
        return $this->title;  
    }

}
