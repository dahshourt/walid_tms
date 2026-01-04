<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrType extends Model
{
    use HasFactory;

    protected $table = 'cr_types';

    protected $fillable = ['name'];

    public function getNameColumn() : string
    {
        return 'name';
    }
}
