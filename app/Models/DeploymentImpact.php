<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeploymentImpact extends Model
{
    use HasFactory; // division_manager_name

    protected $table = 'deployment_impacts';

    protected $fillable = [
        'name',
        'active',

    ];
}
