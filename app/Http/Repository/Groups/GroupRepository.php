<?php

namespace App\Http\Repository\Groups;

use App\Contracts\Groups\GroupRepositoryInterface;
use App\Models\Group; // declare Entities
use App\Models\GroupApplications;
use Illuminate\Support\Arr;

class GroupRepository implements GroupRepositoryInterface
{
    public function StoreApplicationGroup($group_id, $request)
    {
        GroupApplications::where('group_id', $group_id)->delete();
        if (isset($group_id) && ! empty($request)) {

            foreach ($request as $key => $value) {
                $group_applications = new GroupApplications;
                $group_applications->group_id = $group_id;
                $group_applications->application_id = $value;
                $group_applications->save();
            }
        }

        return true;
    }

    public function getAll()
    {
        return Group::with('children', 'parent')->get();
    }

    public function getAllActive()
    {
        return Group::with('children', 'parent')->where('active', '1')->get();
    }

    public function getAllWithFilter($parent_id)
    {
        if ($parent_id) {
            return Group::with('parent')->whereNotNull('parent_id')->get();
        }

        return Group::with('children')->whereNull('parent_id')->get();

    }

    public function create($request)
    {
        $group = Group::create(Arr::except($request, 'application_id'));
        if (isset($request['application_id']) && ! empty($request['application_id'])) {
            $this->StoreApplicationGroup($group->id, $request['application_id']);
        }

        // $this->StoreApplicationGroup($group->id,$request);
        return $group;
    }

    public function delete($id)
    {
        return Group::destroy($id);
    }

    public function update($request, $id)
    {

        $group = Group::where('id', $id)->update($request->except('application_id', '_method', '_token'));
        if (isset($request['application_id']) && ! empty($request['application_id'])) {
            $this->StoreApplicationGroup($id, $request['application_id']);
        }

        return $group;
    }

    public function find($id)
    {
        return Group::with('group_applications')->find($id);
    }

    public function updateactive($active, $id)
    {
        if ($active) {
            return Group::where('id', $id)->update(['active' => '0']);
        }

        return Group::where('id', $id)->update(['active' => '1']);

    }

    public function findByName($name)
    {
        return Group::where('title', $name)->first();
    }
}
