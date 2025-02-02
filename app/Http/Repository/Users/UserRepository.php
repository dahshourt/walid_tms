<?php

namespace App\Http\Repository\Users;
use App\Contracts\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

// declare Entities
use App\Models\User;
use App\Models\UserGroups;
use App\Models\Pivotusersrole;
use App\Http\Repository\Roles\RolesRepository;
use DB;


class UserRepository implements UserRepositoryInterface
{
    public $dev_users = ['ahmed.elfeel' , 'sara.mostafa' , 'mahmoud.bastawisy' , 'tarek.kamaleldin' , 'walid.dahshour' , 'ahmed.o.hasan'];

    public function StoreUserGroups($user_id,$request)
    {
        $groups = $request['group_id'];
        $default_group = $request['default_group'];
        if (!in_array($default_group,$groups)) // check if default group not in groups array then add it to groups array
        {
            $groups[] = $default_group;
        }
        if(isset($user_id) && !empty($groups))
        {
            UserGroups::where('user_id',$user_id)->delete();
            foreach($groups as $value)
            {
                $user_groups = new UserGroups;
                $user_groups->user_id = $user_id;
                $user_groups->group_id = $value;
                $user_groups->save();
            }
        }
        return true;
    }

    public function getAll()
    {
        return User::with('defualt_group','user_report_to.user')->get();
    }

    public function paginateAll()
    {
        return User::whereNotIn('user_name' , $this->dev_users)->latest()->paginate(10);
    }

    public function create($request)
    {
       
     if(!isset($request['email']))
        {
            $request['email']=$request['user_name']."@te.eg";


        }
        $request['flag']="1";
        if($request['user_type']) $request['user_type'] = '1';
        else $request['user_type'] = '0';
        $user = User::create($request);

        // Old Roles 

        if(!empty($request['all_users_roles_values']))
        {
            $report_to = $request['all_users_roles_values'];
        }
        else
        {
            $report_to =  $user->id;
        }

        Pivotusersrole::create([ 'report_to' => $report_to, 'user_id' => $user->id ]);

        unset($request->all_users_roles_values);

        // end old roles

        //new roles and permissions using spatie

        if(isset($request['roles'])){
            //dd($request['roles']);
            $user->assignRole($request['roles']);
        }

        if(isset($request['permissions'])){
            //dd($request['permissions']);
            $user->givePermissionTo($request['permissions']);
        }

        // end new roles and permissions
        $this->StoreUserGroups($user->id,$request);
        return $user;
    }

    public function delete($id)
    {
        return User::destroy($id);
    }

    public function update($request, $id)
    {
        $user = User::findOrFail($id);
        
        if(!empty($request['all_users_roles_values']))
        {
            $report_to = $request['all_users_roles_values'];
        }
        else
        {
            $report_to =  $id;
        }

        // Pivotusersrole::where('user_id',$id)->update([ 'report_to' => $report_to, 'user_id' => $id]);
        Pivotusersrole::updateOrCreate([
            //Add unique field combo to match here
            //For example, perhaps you only want one entry per user:
            'user_id'   => $id,
        ],[
            'report_to' => $report_to,
            'user_id' => $id
        ]);

        if(empty($request['password']))
        {
           $except=['group_id','_method','password','password_confirmation','all_users_roles_values','roles', 'permissions'];
        }
        else
        {

            $except=['group_id','_method','password_confirmation','all_users_roles_values','roles', 'permissions'];
            $request['password']=Hash::make($request['password']);
        }
       
        
        $filteredRequest = \Arr::except($request, $except);
        $user = User::where('id', $id)->update($filteredRequest);


    //new roles and permissions using spatie
    $user = User::findOrFail($id);

    if(isset($request['roles'])){
        //dd($request['roles']);
        $user->syncRoles($request['roles']);
    }else{
        $user->syncRoles([]);
    }

    if(isset($request['permissions'])){
        //dd($request['permissions']);
        $user->syncPermissions($request['permissions']);
    }else{
        $user->syncPermissions([]);
    }

    // end new roles and permissions


        $this->StoreUserGroups($id,$request);
        return $user;
    }
    public function update1($request, $id)
    {
        return User::where('id', $id)->update($request);
    }
    public function find($id)
    {
        return User::whereNotIn('user_name' , $this->dev_users)->with('user_groups.group')->find($id);
    }
	public function updateactive($active,$id)
    {
		if($active)
        {
		    return 	$this->update1(['active'=>'0'],$id);
		}
        else
        {

		    return 	$this->update1(['active'=>'1'],$id);

		}

	}

    public function get_users_with_group_and_role($role_id, $default_group)
    {
        $role = new RolesRepository;
        $parent_role = $role->show($role_id);
        if($parent_role)
        {
            $parent_role_id =  $parent_role->parent_id;
        }
        else
        {
            $parent_role_id = 0;
        }
        // return User::where('default_group',$default_group)->where('role_id','>',$role_id)->get();
        return User::where('default_group',$default_group)->where('role_id',$parent_role_id)->get();
    }


    public function get_user_by_department_id($id)
    {
        return User::where('department_id', $id)->get();
    }

    public function CheckUniqueEmail($email)
    {
        return User::where('email',$email)->first();
        
    }

    public function get_users_cap($system_id)
    {
        return  DB::select
             ("
               SELECT 
                    cps.*,users.user_name,apps.name
                FROM
                    tms_28_1_2025.system_user_cabs cps
                left join users on  users.id  = cps.user_id
                left join applications apps on apps.id = cps.system_id
                where apps.id = ".$system_id."
                ;
             "); 
    }


}
