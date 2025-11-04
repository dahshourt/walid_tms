<?php

namespace App\Http\Controllers\CustomFields;

use App\Factories\ChangeRequest\ChangeRequestFactory;
use App\Factories\CustomField\CustomFieldGroupTypeFactory;
use App\Http\Controllers\Controller;
use App\Http\Repository\Applications\ApplicationRepository;
use App\Http\Repository\Categories\CategoreyRepository;
use App\Http\Repository\Parents\ParentRepository;
use App\Http\Repository\Priorities\priorityRepository;
use App\Http\Repository\Statuses\StatusRepository; // ParentRepository
use App\Http\Repository\Units\UnitRepository; // CategoreyRepository
use App\Http\Repository\Workflow\Workflow_type_repository; // CategoreyRepository
use App\Http\Requests\CustomFields\Api\CustomFieldGroupTypeRequest;
use App\Http\Resources\CustomFieldResource;
use App\Http\Resources\CustomFieldSelectedGroupResource;
use Auth;
use DB;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Log;

class CustomFieldGroupTypeController extends Controller
{
    use ValidatesRequests;

    private $custom_field_group_type;

    private $changerequest;

    public function __construct(ChangeRequestFactory $changerequest, CustomFieldGroupTypeFactory $custom_field_group_type)
    {

        // Ensure the user is authenticated
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            if (! $this->user->hasRole('Super Admin') && ! $this->user->can('Access CustomFields') && ! $this->user->can('Access Advanced Search')) {
                abort(403, 'This action is unauthorized.');
            } else {
                return $next($request);
            }
        });

        $this->custom_field_group_type = $custom_field_group_type::index();
        $this->changerequest = $changerequest::index();
        $view = 'search';
        $route = 'searchs';
        $OtherRoute = 'search';

        $title = 'Search';
        $form_title = 'search';
        view()->share(compact('view', 'route', 'title', 'form_title', 'OtherRoute'));
    }

    public function CustomFieldsByFormType()
    {
        $form_type = request()->form_type;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByFormType($form_type);
        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function CustomFieldsByGroup()
    {
        $group_id = request()->group_id;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByGroup($group_id);
        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function CustomFieldsByWorkFlowType($workflow_type_id, $form_type)
    {

        $workflow_type_id = request()->workflow_type_id;

        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowType($workflow_type_id, $form_type);
        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function CustomFieldsByWorkFlowTypeAndStatus($workflow_type_id, $form_type, $status_id)
    {

        // $workflow_type_id = request()->workflow_type_id;
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeAndStatus($workflow_type_id, $form_type, $status_id);
        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function CustomFieldsByWorkFlowTypeAndViewCrPage($workflow_type_id, $form_type)
    {

        $groupId = request()->header('group');
        $CustomFields = $this->custom_field_group_type->CustomFieldsByWorkFlowTypeViewPage($workflow_type_id, $form_type, $groupId);

        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function AllCustomFieldsWithSelected()
    {
        $column = request()->by;
        $value = request()->value;
        $CustomFields = $this->custom_field_group_type->getAllCustomFieldsWithSelected($column, $value);
        $CustomFields = CustomFieldResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function AllCustomFieldsWithSelectedWithFormType($form_type = '')
    {

        $column = request()->by;
        $value = request()->value;
        $form_type = request()->form_type;
        $CustomFields = $this->custom_field_group_type->AllCustomFieldsWithSelectedWithFormType($column, $value, $form_type);
        $CustomFields = CustomFieldResource::collection($CustomFields);
        $custom_fields = $CustomFields->toArray(request());

        $validation_type_name = $this->custom_field_group_type->getValidations();

        $custom_fields = $CustomFields;

        return view('custom_fields.form', compact('custom_fields', 'validation_type_name'))->render();
    }

    public function AllCustomFieldsWithSelectedByformType(Request $request)
    {
        $this->authorize('Access Advanced Search'); // permission check

        $column = $request->by;
        if (empty($column)) {
            $column = 'form_type';
        }

        $value = $request->value;
        if (empty($value)) {
            $value = '6';
        }
        $CustomFields = $this->custom_field_group_type->getAllCustomFieldsWithSelectedByformType($column, $value);
        $fields = json_decode($CustomFields);
        $statuses = app(StatusRepository::class)->getAll();
        $priorities = app(priorityRepository::class)->getAll();
        $applications = app(ApplicationRepository::class)->getAll();
        $parents = app(ParentRepository::class)->getAll();
        $categories = app(CategoreyRepository::class)->getAll();
        // Ensure $CustomFields is not an array of arrays if not expected
        $units = app(UnitRepository::class)->getAll();
        $workflows = app(Workflow_type_repository::class)->get_all_active_workflow();

        return view('search.advanced_search', compact('fields', 'statuses', 'priorities', 'applications', 'parents', 'categories', 'units', 'workflows'));

    }

    public function AllCustomFieldsSelected()
    {
        // die("walid");
        $CustomFields = $this->custom_field_group_type->AllCustomFieldsSelected();

        $CustomFields = CustomFieldResource::collection($CustomFields);
        $validation_type_name = $this->custom_field_group_type->getValidations();
        $custom_fields = $CustomFields->toArray(request());

        $validation_type_name = $this->custom_field_group_type->getValidations();

        $custom_fields = $CustomFields;

        return view('custom_fields.form', compact('custom_fields', 'validation_type_name'))->render();

    }

    public function index()
    {
        $column = request()->by;
        $value = request()->value;
        // $CustomFields = $this->custom_field_group_type->getAll();
        $CustomFields = $this->custom_field_group_type->getAllByColumnAndValue($column, $value);

        return response()->json(['data' => $CustomFields], 200);
    }

    public function Validation()
    {

        $CustomFields = $this->custom_field_group_type->getValidations();

        return response()->json(['data' => $CustomFields], 200);
    }

    public function store(CustomFieldGroupTypeRequest $request)
    {
        // Log the entire request data

        if (isset($request->custom_field_id)) {
            $column_value = $request->group_id ? $request->group_id : $request->wf_type_id;
            $column = $request->group_id ? 'group_id' : 'wf_type_id';
            // echo "<pre>";
            //         print_r($request->all());
            //         echo "</pre>"; die;
            try {
                DB::beginTransaction();

                // Delete existing custom field group types
                $this->custom_field_group_type->deleteCFs();

                // Loop through each custom field ID
                foreach ($request->custom_field_id as $key => $value) {
                    //  echo $request->sort[$key].'<br>';
                    if (isset($value) && $value != false) {
                        // Log the sort value for each custom field

                        $data = [
                            'form_type' => $request->form_type,
                            'custom_field_id' => $value,
                            'active' => '1',
                            'sort' => $request->sort[$key], // Always include the sort field
                        ];

                        // Conditionally add other fields to the data array
                        if ($request->status_id) {
                            $data['status_id'] = $request->status_id;
                        }
                        if ($request->group_id) {
                            $data['group_id'] = $request->group_id;
                        }
                        if ($request->wf_type_id) {
                            $data['wf_type_id'] = $request->wf_type_id;
                        }
                        if (isset($request->validation_type_id[$key])) {
                            $data['validation_type_id'] = $request->validation_type_id[$key];
                        }
                        if (isset($request->enable[$key])) {
                            $data['enable'] = $request->enable[$key];
                        }

                        // Debug: Print the data array

                        // Create the custom field group type
                        $this->custom_field_group_type->create($data);
                    }
                }

                DB::commit();

                // Redirect back with a success message
                return redirect()->back()->with('success', 'Custom fields saved successfully.');
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error saving custom fields: ' . $e->getMessage());

                return redirect()->back()->with('error', 'Error saving custom fields. Please try again.');
            }
        } else {
            // Return an error response if custom_field_id is not present in the request
            return redirect()->back()->with('error', 'Custom Fields required');

        }
    }

    public function update(CustomFieldGroupTypeRequest $request, $id)
    {
        $CustomField = $this->custom_field_group_type->find($id);
        if (! $CustomField) {
            return response()->json([
                'message' => 'Group Not Exists',
            ], 422);
        }
        $this->CustomField->update($request, $id);

        return response()->json([
            'message' => 'Updated Successfully',
        ]);
    }

    public function show($id)
    {
        $CustomField = $this->custom_field_group_type->find($id);

        return response()->json(['data' => $CustomField], 200);
    }

    public function CustomFieldsForRealeases($form_type)
    {

        $CustomFields = $this->custom_field_group_type->CustomFieldsForReleases($form_type);
        $CustomFields = CustomFieldSelectedGroupResource::collection($CustomFields);

        return response()->json(['data' => $CustomFields], 200);
    }
}
