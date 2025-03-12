<?php
namespace App\Contracts\Defect;

interface DefectRepositoryInterface
{

	public function AddDefect($request);
	public function AddDefectComment($defect_id, $comment);
	public function AddDefectLog($defect_id, $text_log);
	public function AddDefectStatus($defect_id, $previous_status_id, $new_status_id);
	public function Defect_Attach($data, $defect_id);

}