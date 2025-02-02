<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents_crs extends Model
{
    use HasFactory;
    protected $fillable = [
        'application_name', 'name','active'
     ];
}
