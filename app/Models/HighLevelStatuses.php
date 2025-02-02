<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighLevelStatuses extends Model
{
    use HasFactory;
    protected $table = 'high_level_statuses';
    protected $fillable = [
        'name',
        'active',

    ];

    public function status()
    {
        return $this->hasMany(Status::class,'high_level_status_id');
    }

}