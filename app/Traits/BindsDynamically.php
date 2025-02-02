<?php

namespace App\Traits;


use DB;

trait BindsDynamically
{
    protected $tableName = null;

    public function bind(string $tableName)
    {
       $this->setTableName($tableName);
    }


    public function getTableName()
    {
        return $this->tableName;
    }


    public function setTableName(string $tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    public function getDataByDynamicTable()
    {
        $model = $this->getModelFromTable();
        $model = new $model();
        $data = $model::get();
        return $data;
    }

    public function newInstance($attributes = [], $exists = false)
    {
       // Overridden in order to allow for late table binding.

       $model = parent::newInstance($attributes, $exists);
       $model->setTable($this->table);

       return $model;
    }

    public function  getModelByTablename() {
        return '\\App\\Models\\' . \Str::studly(\Str::singular($this->tableName));
    }

    public function getModelFromTable()
    {
        
        foreach( $this->getModels() as $class ) {
            if( is_subclass_of( $class, 'Illuminate\Database\Eloquent\Model' ) ) {
                $model = new $class;
                if ($model->getTable() === $this->tableName) return $class;
            }
        }
        return false;
    }

    

    function getModels(){
        $models = [];
        $modelsPath = app_path('Models');
        $modelFiles = \File::allFiles($modelsPath);
        foreach ($modelFiles as $modelFile) {
            $models[] = '\\App\\Models\\' . $modelFile->getFilenameWithoutExtension();
        }

        return $models;
    }


}