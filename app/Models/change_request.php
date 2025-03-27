<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Change_request extends Model
{
    use HasFactory;
    public $table = 'change_request';
    protected $appends = array('name');
	protected $guarded = [];
    /* protected $fillable = [
        //'cr_no',
        'title',
        'description',
        'active',
        'developer_id',
        'tester_id',
        'designer_id',
        'requester_id',
        'design_duration',
        'start_design_time',
        'end_design_time',
        'develop_duration',
        'start_develop_time',
        'end_develop_time',
        'test_duration',
        'start_test_time',
        'end_test_time',
        'depend_cr_id',
        'helpdesk_id',
        'category_id',
        'priority_id',
        'unit_id',
        // 'department_id',
        'application_id',
        'requester_name',
        'requester_email',
        // 'requester_unit',
        'requester_division_manager',
        'requester_department',
        'application_name',
        'testable',
        'workflow_type_id',
        'division_manager',
        'man_days',
        'release',
        'associated',
        'depend_on',
        //'analysis_feedback',
        //'technical_feedback',
        'approval',
        'need_design',
        'impacted_services',
        'impact_during_deployment',
        'release_delivery_date',
        'release_name',
        'release_receiving_date',
        'need_iot_e2e_testing',
        'te_testing_date',
        'uat_date',
        'cost',
        'uat_duration',
        'parent_id',
        'creator_mobile_number',
        'vendor_id',
        //'need_ux_ui',
        'cr_workload',
		'rtm_member',
		'need_down_time',
		'deployment_impact',
		//'business_feedback',
		'sanity_spoc',
		'postpone'
    ]; */

    protected $hidden = [
        'updated_at',
        'created_at',
    ];



    public function defects()
    {
        return $this->hasMany(Defect::class, 'cr_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class,'cr_id', 'id');
    }

    public function change_request_custom_fields()
    {
        return $this->hasMany(ChangeRequestCustomField::class, 'cr_id', 'id');
    }

    public function Req_status()
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')->select('id', 'new_status_id', 'old_status_id','active');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->select('id', 'name');
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class, 'priority_id')->select('id', 'name');
    }

    // public function unit()
    // {
    //     return $this->belongsTo(unit::class,'unit_id')->select('id','name');
    // }
    public function department()
    {
        return $this->belongsTo(Department::class,'department_id')->select('id','name');
    }
    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id')->select('id', 'name');
    }

    public function depend_cr()
    {
        return $this->belongsTo(Change_request::class, 'depend_cr_id')->select('id', 'title', 'cr_no');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function developer()
    {
        return $this->belongsTo(User::class, 'developer_id')->select('id', 'name', 'user_name', 'email');
    }

    public function tester()
    {
        return $this->belongsTo(User::class, 'tester_id')->select('id', 'name', 'user_name', 'email');
    }

    public function designer()
    {
        return $this->belongsTo(User::class, 'designer_id')->select('id', 'name', 'user_name', 'email');
    }

    public function cab_cr()
    {
        return $this->hasOne(CabCr::class, 'cr_id', 'id')->where('status', '0');
    }

    public function technical_Cr()
    {
        return $this->hasOne(TechnicalCr::class, 'cr_id', 'id')->where('status', '0');
    }

    public function current_status()
    {
        //return $this->HasManyThrough(Status::class,change_request_statuse::class,'cr_id','id','id','new_status_id');
        return $this->HasManyThrough(GroupStatuses::class, Change_request_statuse::class, 'cr_id', 'status_id', 'id', 'new_status_id')
        ->where('group_statuses.type', 1);
    }

    public function RequestStatuses()
    {
        return $this->hasMany(Change_request_statuse::class, 'cr_id', 'id')->where('active', '1')
        ->orderBy('id', 'DESC');
    }

    public function CurrentRequestStatuses()
    {
        return $this->hasOne(Change_request_statuse::class, 'cr_id', 'id')->where('active', '1');
    }

    public function ListCurrentStatus()
    {
        if(session('default_group')){
            $group = session('default_group');

        }else {
            $group = auth()->user()->default_group;
        }
        $view_statuses = GroupStatuses::where('group_id', $group)->where('type', 2)->get()->pluck('status_id');
        $status = Change_request_statuse::where('cr_id', $this->id)->whereIn('new_status_id', $view_statuses)->where('active', '1')->first();

        return $status;
    }


    public function division_manger()
    {
        return $this->belongsTo(User::class, 'division_manager_id');
    }
    public function attachments()
    {
        return $this->hasMany(Attachements_crs::class, 'cr_id');
    }


    public function getNameAttribute()
    {
        return $this->title;
    }
    public function release()
    {
        return $this->belongsTo(Release::class, 'release_name', 'id');
    }

    public function get_releases()
    {
        $list_releases = Release::whereDate('go_live_planned_date', '>', now())->get();
        return $list_releases;
    }

    public function getCurrentStatusOld()
    {
        
        $status = Change_request_statuse::where('cr_id', $this->id)->where('active', '1')->first();
        $workflow = NewWorkFlow::where('from_status_id',$status->old_status_id)->where('type_id',$this->workflow_type_id)->first();
        $status->same_time = isset($workflow) && $workflow->same_time ? $workflow->same_time : 0;
        $status->to_status_label = isset($workflow) && $workflow->to_status_label ? $workflow->to_status_label : "";
        return $status;
    }


    public function getCurrentStatus()
    {
        
        //$status = Change_request_statuse::where('cr_id', $this->id)->where('active', '1')->first();
        if(session('default_group')){
            $group = session('default_group');

        }else {
            $group = auth()->user()->default_group;
        }
        $view_statuses = GroupStatuses::where('group_id', $group)->where('type', 2)->get()->pluck('status_id');
        $status = Change_request_statuse::where('cr_id', $this->id)->whereIn('new_status_id', $view_statuses)->where('active', '1')->first();
        if($status)
        {
            $workflow = NewWorkFlow::where('from_status_id',$status->old_status_id)->where('type_id',$this->workflow_type_id)->first();
       

            $status->same_time = isset($workflow) && $workflow->same_time ? $workflow->same_time : 0;
            $status->to_status_label = isset($workflow) && $workflow->to_status_label ? $workflow->to_status_label : "";
        }
        else
        {
            $status = Change_request_statuse::where('cr_id', $this->id)->where('active', '1')->first();
            if($status)
			{
				$workflow = NewWorkFlow::where('from_status_id',$status->old_status_id)->where('type_id',$this->workflow_type_id)->first();
				$status->same_time = isset($workflow) && $workflow->same_time ? $workflow->same_time : 0;
				$status->to_status_label = isset($workflow) && $workflow->to_status_label ? $workflow->to_status_label : "";
			}
            else
			{
				$status = Change_request_statuse::where('cr_id', $this->id)->orderBy('id', 'desc')->first();
				if($status)
				{
					$workflow = NewWorkFlow::where('from_status_id',$status->old_status_id)->where('type_id',$this->workflow_type_id)->first();
					$status->same_time = isset($workflow) && $workflow->same_time ? $workflow->same_time : 0;
					$status->to_status_label = isset($workflow) && $workflow->to_status_label ? $workflow->to_status_label : "";
				}
			}
        }
       
        //dd($status);
        return $status;
    }
	
	
	

}
