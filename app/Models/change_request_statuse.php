<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Change_request_statuse extends Model
{
    use HasFactory;
    public  $table = "change_request_statuses";
    protected $fillable = [
        'cr_id',
        'old_status_id',
        'new_status_id',
        'user_id',
        'active',
        'sla',
        'sla_dif',
        'assignment_user_id',

    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            $model->updated_at = null;
        });
    }

    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function CR()
    {
        return $this->belongsTo(Change_request::class,'cr_id');// how is it work "belongs_to" ? the right is "belongsTo"

    }

    public function change_request_data()
    {
        return $this->belongsTo(Change_request::class,'cr_id' ,'id');

    }

    public function status()
    {
        return $this->belongsTo(Status::class,'new_status_id');
    }

    public function current_status_for_group()
    {
        return $this->belongsTo(GroupStatuses::class,'new_status_id','status_id');
    }

    // mahmoud's update
    public function ChangeRequest()
    {
        return $this->belongsTo(Change_request::class,'cr_id');
    }

    public function wokflow()
    {
        return $this->belongsTo(NewWorkFlow::class,'new_status_id','from_status_id');
    }






}
