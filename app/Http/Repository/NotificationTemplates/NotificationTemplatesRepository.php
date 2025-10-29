<?php

namespace App\Http\Repository\NotificationTemplates;

use App\Contracts\NotificationTemplates\NotificationTemplatesRepositoryInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\NotificationTemplate;

use Auth;

class NotificationTemplatesRepository implements NotificationTemplatesRepositoryInterface
{
    public function getAll()
    { 
        return NotificationTemplate::paginate(10);
    } // end method


    public function create($request)
    {

        $validator = Validator::make($request, [
            'template_name' => 'required|unique:notification_templates,name',
            'template_subject' => 'required',
            'template_body' => 'required'
            
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        NotificationTemplate::create(['name' => $request['template_name']  , 'subject' => $request['template_subject'] , 'body' => $request['template_body']?? null]);  //
        //return true;
        return redirect()->back()->with('status' , 'Permission Added Successfully' );

    }  // end method

    public function find($id)
    {
        return NotificationTemplate::find($id);
    } // end method

    public function list()
    {
        return NotificationTemplate::all();
    } //end method


    public function update($request , $id){

        $validator = Validator::make($request, [
            'template_name' => 'required|unique:notification_templates,name,'.$id,
            'template_subject' => 'required',
            'template_body' => 'required'
            
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        NotificationTemplate::where('id' , $id)->update(['name' => $request['template_name']  , 'subject' => $request['template_subject'] , 'body' => $request['template_body']?? null]);



    } //end method 


    public function delete($id)
    {
        return NotificationTemplate::find($id)->delete();
    } //end method
}
