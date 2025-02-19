<?php


use Illuminate\Support\Facades\Route;





/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



//Auth::routes();
Route::get('login', 'Auth\CustomAuthController@index')->name('login');
Route::post('login', 'Auth\CustomAuthController@login')->name('login.custom');
Route::get('/logout', 'Auth\LoginController@logout');
Route::middleware(['auth'])->group(
    function () {
        
        Route::get('/statistics', 'HomeController@StatisticsDashboard');
        Route::get('/dashboard', 'HomeController@StatisticsDashboard');

        Route::get('get_workflow/subtype/all', 'Workflow\Workflow_type@Allsubtype');
        Route::get('customs/field/group/type/selected/{form_type?}', 'CustomFields\CustomFieldGroupTypeController@AllCustomFieldsWithSelectedWithFormType');
        Route::get('/', 'HomeController@index')->name('home');
        Route::post('/charts_dashboard', 'HomeController@dashboard');
        
        Route::get('/application_based_on_workflow', 'HomeController@application_based_on_workflow');
        //Route::get('/dashboard', 'HomeController@dashboard');
        Route::post('custom/field/group/type', 'CustomFields\CustomFieldGroupTypeController@store')->name('custom.fields.store');
        Route::get('/custom_fields/create', 'CustomFields\CustomFieldController@create')->name('custom.fields.create');
        Route::get('/custom_fields/createCF', 'CustomFields\CustomFieldController@createCF')->name('custom.fields.createCF');

        Route::get('/custom_fields/search', 'CustomFields\CustomFieldController@search')->name('custom.fields.search');
        // Route::get('/custom_fields/search/special', 'CustomFields\CustomFieldController@special')
        // ->name('custom.fields.special')
        // ->defaults('parent', true); 
        Route::get('/custom_fields/view', 'CustomFields\CustomFieldController@view')->name('custom.fields.view');//viewupdate
        Route::get('/custom_fields/viewCF', 'CustomFields\CustomFieldController@viewCF')->name('custom.fields.viewCF');//viewupdate
        Route::get('/custom_fields/viewupdate', 'CustomFields\CustomFieldController@viewupdate')->name('custom.fields.viewupdate');//viewupdate
        Route::get('groups/list/child', ['uses' => 'CustomFields\CustomFieldController@special', 'parent' => true]) ->name('custom.fields.special');   
        Route::get('groups/list/specialview', ['uses' => 'CustomFields\CustomFieldController@specialview', 'parent' => true]) ->name('custom.fields.special.view');     
        Route::get('groups/list/specialviewresult', ['uses' => 'CustomFields\CustomFieldController@specialviewresult', 'parent' => true]) ->name('custom.fields.special.viewresult'); 
        Route::get('groups/list/specialviewupdate', ['uses' => 'CustomFields\CustomFieldController@specialviewupdate', 'parent' => true]) ->name('custom.fields.special.viewupdate');     
        Route::get('groups/list/specialviewsearch', ['uses' => 'CustomFields\CustomFieldController@specialviewsearch', 'parent' => true]) ->name('custom.fields.special.viewsearch'); 
        Route::get('groups/list/specialviewadvanced', ['uses' => 'CustomFields\CustomFieldController@specialviewadvanced', 'parent' => true]) ->name('custom.fields.special.viewadvanced'); 

        Route::get('custom-fields/load', 'CustomFields\CustomFieldController@loadCustomFields');
        Route::get('customs/field/special', 'CustomFields\CustomFieldGroupTypeController@AllCustomFieldsSelected');
        Route::get('/select/group', 'HomeController@SelectGroup')->name('select.group');
        Route::post('/select/group', 'HomeController@storeGroup')->name('store.group');
        Route::resource('users', Users\UserController::class);
        Route::post('users/export-table', 'Users\UserController@exportTable')->name('export.users.table');

        Route::post('user/updateactive', 'Users\UserController@updateactive');//groupco
        Route::post('groups/updateactive', 'Groups\GroupController@updateactive');
        Route::resource('statuses', Statuses\StatusController::class);
        Route::post('status/updateactive', 'Statuses\StatusController@updateactive');
        Route::resource('division_manager', Division_manager\Division_managerController::class);
		 Route::resource('stages', Stages\StageController::class);
       Route::post('stage/updateactive', 'Stages\StageController@updateactive');
       Route::resource('parents', Parents\ParentController::class);
        Route::post('parent/updateactive', 'Parents\ParentController@updateactive');
       Route::resource('high_level_status', highLevelStatuses\highLevelStatusesControlller::class);
       Route::post('high_level_status/updateactive', 'highLevelStatuses\highLevelStatusesControlller@updateactive');
       Route::resource('workflows', Workflow\WorkflowController::class);
       
       Route::resource('searchs', Search\SearchController::class);
      // Route::get('/search/result', 'Search\SearchController@search_result');

       Route::get('my_assignments', 'ChangeRequest\ChangeRequestController@my_assignments');
       Route::resource('groups', Groups\GroupController::class);

       //Route::resource('workflows', Workflow\WorkflowController::class);
       Route::resource('NewWorkFlowController', Workflow\NewWorkFlowController::class);
       Route::get('workflow/list/all', 'Workflow\NewWorkFlowController@ListAllWorkflows');
       Route::post('workflow2/updateactive', 'Workflow\NewWorkFlowController@updateactive');
       //Route::resource('searchs', Search\SearchController::class);
       Route::get('/search/result', 'Search\SearchController@search_result');
      // Route::get('/search/advanced_search', 'Search\SearchController@advanced_search');
       Route::get('advanced/search/result', 'Search\SearchController@AdvancedSearchResult')->name('advanced.search.result');;
        Route::get('/search/advanced_search', 'CustomFields\CustomFieldGroupTypeController@AllCustomFieldsWithSelectedByformType')->name('advanced.search');

       Route::resource('applications', Applications\ApplicationController::class);
      // Route::post('applications/updateactive', 'Applications\ApplicationController@updateactive');
      
      Route::post('advanced-search-requests/export', 'Search\SearchController@AdvancedSearchResultExport')->name('advanced.search.export');;

       Route::resource('rejection_reasons', RejectionReasons\RejectionReasonsController::class);
       Route::post('rejection_reasons/updateactive', 'RejectionReasons\RejectionReasonsController@updateactive');
        Route::resource('roles', Roles\RolesController::class);//
        Route::resource('permissions', Permissions\PermissionsController::class);//
        Route::resource('mail_templates', MailTemplates\MailTemplatesController::class);//

        //Route::middleware('group')->group( function () {
        
        //});
        Route::post('change_request/listCRsUsers', 'ChangeRequest\ChangeRequestController@Crsbyusers');
        Route::get('change_request/listcrsbyuser', 'ChangeRequest\ChangeRequestController@list_crs_by_user');
        
        Route::resource('change_request', 'ChangeRequest\ChangeRequestController');
        Route::get('change_request1/asd/{group?}', 'ChangeRequest\ChangeRequestController@asd')->name('change_request.asd');
        Route::post('/select-group/{group}', 'ChangeRequest\ChangeRequestController@selectGroup')->name('select_group');

       
        Route::post('/change-requests/reorder', 'ChangeRequest\ChangeRequestController@reorderChangeRequest')->name("change-requests.reorder");
        Route::get('/change-requests/reorder/home', 'ChangeRequest\ChangeRequestController@reorderhome')->name("change-requests.reorder.home");

        Route::get('change_request/workflow/type', 'ChangeRequest\ChangeRequestController@Allsubtype');
        Route::get('files/download/{id}','ChangeRequest\ChangeRequestController@download')->name('files.download');

        // send mail routes
        Route::get('manual_email', 'Mail\MailController@index');
        Route::get('send-mail', 'Mail\MailController@sendMailByTemplate');

        Route::get('releases/show_crs/asd', 'Releases\CRSReleaseController@show_crs');
        Route::get('releases/home', 'Releases\CRSReleaseController@reorderhome');

        // release routes

        Route::resource('releases', Releases\ReleaseController::class);
   
		Route::get('releases/show_release/{id}', 'Releases\ReleaseController@show_release');

        Route::get('release/logs/{id}', 'Releases\ReleaseController@ReleaseLogs');

        Route::get('update_release_its_crs', 'Releases\ReleaseController@update_release_its_crs');

        // check division manager using active directory route

        Route::post('/check-division-manager', 'Division_manager\Division_managerController@ActiveDirectoryCheck');

        // approve cr

        Route::get('cr/{id}' , 'ChangeRequest\ChangeRequestController@show')->name('show.cr');
        Route::get('change_request/{id}/edit' , 'ChangeRequest\ChangeRequestController@edit')->name('edit.cr');


        Route::resource('cab_users', 'CabUser\CabUserController');
        Route::post('cab_user/updateactive', 'CabUser\CabUserController@updateactive');
});

/// user routes
/*
Route::middleware(['auth','role:user|admin'])->group(
    function () {
        Route::get('/', 'HomeController@index')->name('home');
        Route::resource('searchs', Search\SearchController::class);
      // Route::get('/search/result', 'Search\SearchController@search_result');

       Route::get('my_assignments', 'ChangeRequests\ChangeRequestController@my_assignments');
       Route::resource('groups', Groups\GroupController::class);

       //Route::resource('workflows', Workflow\WorkflowController::class);
       Route::resource('NewWorkFlowController', Workflow\NewWorkFlowController::class);
       Route::post('workflow2/updateactive', 'Workflow\NewWorkFlowController@updateactive');
       //Route::resource('searchs', Search\SearchController::class);
       Route::get('/search/result', 'Search\SearchController@search_result');
      // Route::get('/search/advanced_search', 'Search\SearchController@advanced_search');
       Route::post('advanced/search/result', 'Search\SearchController@AdvancedSearchResult')->name('advanced.search.result');;
       Route::get('my_assignments', 'ChangeRequests\ChangeRequestController@my_assignments');
       Route::get('/search/advanced_search', 'CustomFields\CustomFieldGroupTypeController@AllCustomFieldsWithSelectedByformType')->name('advanced.search');

});*/
