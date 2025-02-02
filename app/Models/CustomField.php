<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\BindsDynamically;

class CustomField extends Model
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
    protected $table = 'custom_fields';
    protected $fillable = [ 'type','active','name','label','class','default_value','related_table' ];


    public function custom_field_group()
    {
        return $this->hasMany(CustomFieldGroup::class);
    }

    public function custom_field_by_group()
    {
        return $this->hasMany(CustomFieldGroup::class);
    }

    public function custom_field_by_workflow()
    {
        return $this->hasMany(CustomFieldGroup::class);
    }


    public function getCustomFieldValue()
    {
        // if ($this->related_table === 'users') {
        //     // Fetch users with their default group names
        //     return \DB::table('users')
        //         ->leftJoin('groups', 'users.default_group', '=', 'groups.id') // Adjust table and column names
        //         ->select('users.*', 'groups.title as group_name') // Include group name
        //         ->get();
        // }
    
        // Default behavior for other related tables
        return $this->setTableName($this->related_table)->getDataByDynamicTable();
    }
    


    public static function findId($name)
    {
        return static::where('name', $name)->first();
    }


    
}
