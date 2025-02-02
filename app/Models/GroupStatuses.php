<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupStatuses extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'group_id',
        'type'
    ];


     /**
     * The code of group type set by.
     *
     * @var string
     */
    CONST SETBY = 1;

    /**
     * The code of group type view by.
     *
     * @var string
     */
    CONST VIEWBY = 2;



    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // public function view_group()
    // {
    //     return $this->belongsTo(Group::class);
    // }

}
