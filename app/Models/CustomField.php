<?php

namespace App\Models;

use App\Traits\BindsDynamically;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'created_at',
    ];

    protected $table = 'custom_fields';

    protected $fillable = ['type', 'active', 'name', 'label', 'class', 'default_value', 'related_table', 'log_message'];

    public static function findId($name)
    {
        return static::where('name', $name)->first();
    }

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
        if (empty($this->related_table)) {
            Log::warning('CustomField has empty related_table', [
                'custom_field_id' => $this->id,
                'custom_field_name' => $this->name
            ]);
            return collect([]);
        }
        
        return $this->setTableName($this->related_table)->getDataByDynamicTable();
    }

    public function getSpecificCustomFieldValues(array $selected, ?string $columnName = null, ?string $pluckColumn = null)
    {
        return $this->setTableName($this->related_table)->getCustomDataByDynamicTable($selected, $columnName, $pluckColumn);
    }
}
