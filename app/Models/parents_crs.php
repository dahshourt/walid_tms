<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parents_crs extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_name', 'name', 'active', 'file',
    ];

    public function change_request()
    {
        return $this->belongsTo(Change_request::class, 'name'); // how is it work "belongs_to" ? the right is "belongsTo"

    }
}
