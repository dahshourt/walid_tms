<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BindsDynamically;

class ChangeRequestCustomField extends Model
{

    use BindsDynamically;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    protected $table = 'change_request_custom_fields';
    protected $fillable = [ 'cr_id','custom_field_id','custom_field_name','custom_field_value' ];


    public function custom_field()
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id', 'id');
    }

    public function change_request()
    {
        return $this->belongsTo(Change_request::class,'cr_id');

    }

    
}
