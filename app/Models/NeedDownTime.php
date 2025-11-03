<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NeedDownTime extends Model
{
    use HasFactory; // division_manager_name

    protected $table = 'need_down_times';

    protected $fillable = [
        'name',
        'active',

    ];
}
