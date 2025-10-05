<?php

namespace App\Http\Repository\Prerequisites;
use App\Contracts\Prerequisites\PrerequisitesRepositoryInterface;

// declare Entities
use App\Models\Prerequisite;
use Illuminate\Support\Facades\DB;
use App\Models\PrerequisiteAttachment;
use App\Models\PrerequisiteComment;
use App\Models\PrerequisiteLog;
use App\Models\Status;

class PrerequisitesRepository implements PrerequisitesRepositoryInterface
{

    
    public function getAll()
    {
        return Prerequisite::all();
    }

    public function create($requestData)
    {
        return DB::transaction(function () use ($requestData) {
            $data = collect($requestData);
            
            $prerequisite = Prerequisite::create($data->except(['comments', 'attachments'])->all());

            if (isset($requestData['comments']) && !empty($requestData['comments'])) {
                $prerequisite->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $requestData['comments']
                ]);
            }

            if (isset($requestData['attachments'])) {
                $this->handleAttachments($requestData['attachments'], $prerequisite->id);
            }
            $prerequisite->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => 'Prerequisite was created',
            ]);

            return $prerequisite;
        });
    }

    protected function handleAttachments($file, $prerequisiteId)
    {
        if ($file->isValid()) { 
            $uploadPath = public_path('uploads/prerequisites');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $filename = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
            
            if ($file->move($uploadPath, $filename)) {
                
                PrerequisiteAttachment::create([
                    'prerequisite_id' => $prerequisiteId,
                    'user_id' => auth()->id(),
                    'file' => $filename,
                ]);
            }
        }
    }

    public function delete($id)
    {
        return Prerequisite::destroy($id);
    }


    public function update($request, $model)
    {
        return DB::transaction(function () use ($request, $model) {
            $data = collect($request);

            // update main record
            $model->update($data->except(['comments', 'attachments'])->all());

            // add comment if provided
            if (isset($request['comments']) && !empty($request['comments'])) {
                $model->comments()->create([
                    'user_id' => auth()->id(),
                    'comment' => $request['comments']
                ]);
            }

            // add new attachments if provided
            if (isset($request['attachments'])) {
                $this->handleAttachments($request['attachments'], $model->id);
            }

            // add a log
            $model->logs()->create([
                'user_id' => auth()->id(),
                'log_text' => 'Prerequisite was updated',
            ]);

            return $model;
        });
    }


    public function find($id)
    {
        return Prerequisite::find($id);
    }

    public function paginateAll()
    {
        if(empty($group)){
            if(session('default_group')){
                $group = session('default_group');
    
            }else {
                $group = auth()->user()->default_group;
            }
        }

        $userId = auth()->id();

        $openStatus = Status::where('status_name', 'Open')->value('id');
        $pendingStatus = Status::where('status_name', 'Pending')->value('id');
        $closedStatus = Status::where('status_name', 'Closed')->value('id');
    
        $query = Prerequisite::query();
    
        $query->where(function ($q) use ($group, $openStatus) {
            $q->where('group_id', $group)
              ->where('status_id', $openStatus);
        })
        ->orWhere(function ($q) use ($userId, $pendingStatus, $closedStatus) {
            $q->where('created_by', $userId)
              ->whereIn('status_id', [$pendingStatus, $closedStatus]);
        });
    
        return $query->paginate(10);
    }

}