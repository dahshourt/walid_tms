<?php

namespace App\Http\Controllers\NotificationTemplates;

use App\Factories\NotificationTemplates\NotificationTemplatesFactory;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationTemplatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $NotificationTemplates;

    public function __construct(NotificationTemplatesFactory $NotificationTemplates)
    {

        $this->NotificationTemplates = $NotificationTemplates::index();
        $this->view = 'NotificationTemplates';
        $view = 'NotificationTemplates';
        $route = 'NotificationTemplates';
        $OtherRoute = 'NotificationTemplates';

        $title = 'Notification Templates';
        $form_title = 'Notification Templates';
        view()->share(compact('view', 'route', 'title', 'form_title', 'OtherRoute'));

    }

    public function index()
    {
        $collection = $this->NotificationTemplates->getAll();

        return view("$this->view.index", compact('collection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("$this->view.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $store = $this->NotificationTemplates->create($request->all());

        return redirect()->back()->with('status', 'Template Added Successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $row = $this->NotificationTemplates->find($id);

        return view("$this->view.show", compact('row'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = $this->NotificationTemplates->find($id);

        return view("$this->view.edit", compact('row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->NotificationTemplates->update($request->all(), $id);

        return redirect()->back()->with('status', 'Template Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->NotificationTemplates->delete($id);

        return redirect()->back()->with('success', 'Template deleted successfully!');
    }
}
