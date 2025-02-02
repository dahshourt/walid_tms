<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;


    protected $fillable = [
       'id', 'name','active','workflow_type_id'
    ];
    public function workflow_type()
    {
        return $this->belongsTo(WorkFlowType::class);
    }
    public function group_applications()
    {
        return $this->hasMany(GroupApplications::class);
    }
}



