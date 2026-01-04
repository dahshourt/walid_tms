<?php

namespace App\Traits;

use App\Models\NewWorkFlowStatuses;
use App\Models\Status;
use App\Models\SystemUserCab;
use File;
use Illuminate\Support\Facades\Log;
use Str;

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
        if ($this->tableName == 'users') {
            $data = $model::where('active', '1')->get();
        } else {
            $data = $model::get();
        }

        return $data;
    }

    public function getCustomDataByDynamicTable(array $selectedValues, ?string $columnName = null,  ?string $pluckColumn = null)
    {
        try {

            $model = $this->getModelFromTable();
            $model = new $model();

            if ($model instanceof Status) {
                $new_work_flow_status = NewWorkFlowStatuses::with('to_status')->whereIn('new_workflow_id', $selectedValues)->first();

                return collect($new_work_flow_status?->to_status?->status_name);
            }

            $query = $model::whereIn($columnName ?? 'id', $selectedValues);

            if (!$model instanceof SystemUserCab) {
                $pluckColumn = $model->getNameColumn();
            }

            return $pluckColumn ? $query->pluck($pluckColumn) : $query->get();
        } catch (\Throwable $exception) {
            Log::error('Error while getting custom data by dynamic table', [
                'message' => $exception->getMessage(),
                'exception' => $exception->getTraceAsString(),
                'line' => $exception->getLine(),
            ]);

            return collect([]);
        }
    }

    public function newInstance($attributes = [], $exists = false)
    {
        // Overridden in order to allow for late table binding.

        $model = parent::newInstance($attributes, $exists);
        $model->setTable($this->table);

        return $model;
    }

    public function getModelByTablename()
    {
        return '\\App\\Models\\' . Str::studly(Str::singular($this->tableName));
    }

    public function getModelFromTable()
    {
        foreach ($this->getModels() as $class) {
            if (is_subclass_of($class, 'Illuminate\Database\Eloquent\Model')) {
                $model = new $class;
                if ($model->getTable() === $this->tableName) {
                    return $class;
                }
            }
        }

        return false;
    }

    public function getModels()
    {
        $models = [];
        $modelsPath = app_path('Models');
        $modelFiles = File::allFiles($modelsPath);
        foreach ($modelFiles as $modelFile) {
            $models[] = '\\App\\Models\\' . $modelFile->getFilenameWithoutExtension();
        }

        return $models;
    }
}
