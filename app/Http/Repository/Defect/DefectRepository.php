<?php

namespace App\Http\Repository\Defect;

use App\Contracts\Factories\Defect\DefectFactory;
use App\Contracts\Defect\DefectRepositoryInterface;
use App\Models\Defect;
use App\Models\DefectComment;
use App\Models\DefectLog;
use App\Models\DefectStatus;
use App\Models\DefectAttachment;
use Auth;
class DefectRepository implements DefectRepositoryInterface
{
    public function AddDefect($request)
    { 
        return $defect = Defect::create([
            'cr_id'      => $request->cr_id,
            'subject'    => $request->title,
            'group_id'   => $request->technical_team,
            'status_id'  =>$request->defect_status,
            'created_by' => Auth::user()->id,
        ]);
        
    }

    public function AddDefectComment($defect_id, $comment)
    {
        return $defect = DefectComment::create([
            'defect_id' => $defect_id,
            'user_id'   => Auth::user()->id,
            'comment'   => $comment,
        ]);
    }

    public function AddDefectLog($defect_id, $text_log)
    {
        return $defect = DefectLog::create([
            'defect_id' => $defect_id,
            'user_id'   => Auth::user()->id,
            'log_text'  => $text_log,
        ]);
    }

    public function AddDefectStatus($defect_id, $previous_status_id, $new_status_id)
    {
        return $defect = DefectStatus::create([
            'defect_id'           => $defect_id,
            'user_id'             => Auth::user()->id,
            'previous_status_id'  => $previous_status_id,
            'new_status_id'       => $new_status_id
        ]);
    }

    public function Defect_Attach($data, $defect_id)
    {
        foreach ($data as  $key=>$file) {
            $filename = rand(10,1000)."_".$file->getClientOriginalName();
            $file->move(public_path('uploads/defects'), $filename);
            $defect_attachment = DefectAttachment::create([
                'defect_id' => $defect_id,
                'user_id'   => Auth::user()->id,
                'file'      => $filename,
            ]);
        }
    }

    public function get_defect_data($defect_id)
    {
        return Defect::find($defect_id);
    }

    public function get_defect_comments($defect_id)
    {
        return DefectComment::where("defect_id", $defect_id)->get();
    }

    public function get_defect_attachments($defect_id)
    {
        return DefectAttachment::where("defect_id", $defect_id)->get();
    }
}
