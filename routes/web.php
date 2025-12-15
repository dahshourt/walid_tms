<?php

use Illuminate\Support\Facades\Route;
use App\Services\EwsMailReader;
use App\Http\Controllers\Sla\SlaCalculationController;
use App\Http\Controllers\ChangeRequest\Api\EmailApprovalController;
use App\Http\Controllers\Report\ReportController;
use App\Http\Controllers\ConfigurationController;






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
Route::get('/mail_approve', [EmailApprovalController::class, 'ApproveMail']);
Route::get('login', 'Auth\CustomAuthController@index')
    ->middleware('guest')
    ->name('login');
Route::post('login', 'Auth\CustomAuthController@login')->name('login.custom')->middleware('throttle:5,1');
Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/inactive-logout','Auth\CustomAuthController@inactive_logout')->name('inactive-logout');

Route::get('/check-active','Auth\CustomAuthController@check_active')->name('check-active');

Route::get('/cr/division_manager/action', 'ChangeRequest\ChangeRequestController@handleDivisionManagerAction')
    ->name('cr.division_manager.action');

Route::middleware(['auth'])->group(
    function () {
       Route::get('/change_request/approved_active', 'ChangeRequest\ChangeRequestController@handleDivisionManagerAction1');
       Route::get('/change_request2/approved_active_cab', 'ChangeRequest\ChangeRequestController@handlePendingCap');
       Route::get('/change_request2/approved_continue', 'ChangeRequest\ChangeRequestController@approved_continue');

       // Route::post('/change_request/rejected_active', 'ChangeRequest\ChangeRequestController@rejected_active');
        Route::get('/statistics', 'HomeController@StatisticsDashboard');
        Route::get('/dashboard', 'HomeController@StatisticsDashboard');

        //Route::get('get_workflow/subtype/all', 'Workflow\Workflow_type@Allsubtype');
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
        Route::get('statuses/export', 'Statuses\StatusController@export')->name('statuses.export');
        Route::resource('statuses', Statuses\StatusController::class);
        Route::post('status/updateactive', 'Statuses\StatusController@updateactive');
        Route::resource('division_manager', Division_manager\Division_managerController::class);

        Route::resource('directors', 'Director\DirectorController')->except(['show', 'destroy']);
        Route::post('directors/updateactive', 'Director\DirectorController@updateStatus')->name('directors.updateStatus');

        // Custom Fields Management Routes
        Route::resource('custom-fields', 'CustomField\CustomFieldController')->except(['show', 'destroy']);
        Route::post('custom-fields/updateactive', 'CustomField\CustomFieldController@updateStatus')->name('custom-fields.updateStatus');
        Route::get('custom-fields/get-table-options', 'CustomField\CustomFieldController@getTableOptions')->name('custom-fields.get-table-options');

        // Hold Reasons Management Routes
        Route::resource('hold-reasons', 'HoldReasonController')->except(['show', 'destroy']);
        Route::post('hold-reasons/update-status', 'HoldReasonController@updateStatus')->name('hold-reasons.update-status');

        // KPI Configuration Routes
        Route::resource('kpi-types', 'KpiType\KpiTypeController')->except(['show', 'destroy']);
        Route::post('kpi-types/update-status', 'KpiType\KpiTypeController@updateStatus')->name('kpi-types.update-status');

        Route::resource('kpi-pillars', 'KpiPillar\KpiPillarController')->except(['show', 'destroy']);
        Route::post('kpi-pillars/update-status', 'KpiPillar\KpiPillarController@updateStatus')->name('kpi-pillars.update-status');

        Route::resource('kpi-initiatives', 'KpiInitiative\KpiInitiativeController')->except(['show', 'destroy']);
        Route::post('kpi-initiatives/update-status', 'KpiInitiative\KpiInitiativeController@updateStatus')->name('kpi-initiatives.update-status');

        Route::resource('kpi-sub-initiatives', 'KpiSubInitiative\KpiSubInitiativeController')->except(['show', 'destroy']);
        Route::post('kpi-sub-initiatives/update-status', 'KpiSubInitiative\KpiSubInitiativeController@updateStatus')->name('kpi-sub-initiatives.update-status');

        Route::resource('units', 'Units\UnitController')->except(['show', 'destroy']);
        Route::post('units/updateactive', 'Units\UnitController@updateStatus')->name('units.updateStatus');

		 Route::resource('stages', Stages\StageController::class);
         Route::resource('requester-department', RequesterDepartment\RequesterDepartmentController::class);
        Route::post('requester-department/updateactive', 'RequesterDepartment\RequesterDepartmentController@updateactive')
        ->name('requester-department.update-active');

       Route::post('stage/updateactive', 'Stages\StageController@updateactive');
       Route::resource('parents', Parents\ParentController::class);
        Route::post('parent/updateactive', 'Parents\ParentController@updateactive');
        Route::get('list/CRs/by/workflowtype', 'Parents\ParentController@ListCRsbyWorkflowtype');
		Route::get('parent/file/download/{id}','Parents\ParentController@download')->name('parent.download');



       Route::resource('high_level_status', highLevelStatuses\highLevelStatusesControlller::class);
       Route::post('high_level_status/updateactive', 'highLevelStatuses\highLevelStatusesControlller@updateactive');
       //Route::resource('workflows', Workflow\WorkflowController::class);

       Route::resource('searchs', Search\SearchController::class);
      // Route::get('/search/result', 'Search\SearchController@search_result');

       Route::get('my_assignments', 'ChangeRequest\ChangeRequestController@my_assignments');
       Route::resource('groups', Groups\GroupController::class);
	   Route::get('group/statuses/{id}', 'Groups\GroupController@GroupStatuses');
	   Route::post('group/store/statuses/{id}', 'Groups\GroupController@StoreGroupStatuses');

       //Route::resource('workflows', Workflow\WorkflowController::class);
       Route::resource('NewWorkFlowController', Workflow\NewWorkFlowController::class);
       Route::get('workflow/list/all', 'Workflow\NewWorkFlowController@ListAllWorkflows');
       Route::get('workflow/same/from/status', 'Workflow\NewWorkFlowController@SameFromWorkflow');
       Route::post('workflow2/updateactive', 'Workflow\NewWorkFlowController@updateactive');
       Route::get('workflow/export', 'Workflow\NewWorkFlowController@exportWorkflows')->name('workflow.export');
       //Route::resource('searchs', Search\SearchController::class);
       Route::get('/search/result', 'Search\SearchController@search_result');
      // Route::get('/search/advanced_search', 'Search\SearchController@advanced_search');
       Route::get('advanced/search/result', 'Search\SearchController@AdvancedSearchResult')->name('advanced.search.result');;
        Route::get('/search/advanced_search', 'CustomFields\CustomFieldGroupTypeController@AllCustomFieldsWithSelectedByformType')->name('advanced.search');

		Route::resource('applications', Applications\ApplicationController::class);
		Route::post('application/updateactive', 'Applications\ApplicationController@updateactive');
		Route::get('app/file/download/{id}','Applications\ApplicationController@download')->name('app.download');

		Route::post('advanced-search-requests/export', 'Search\SearchController@AdvancedSearchResultExport')->name('advanced.search.export');;

       Route::resource('rejection_reasons', RejectionReasons\RejectionReasonsController::class);
       Route::post('rejection_reasons/updateactive', 'RejectionReasons\RejectionReasonsController@updateactive');
        Route::resource('roles', Roles\RolesController::class);//
        Route::resource('permissions', Permissions\PermissionsController::class);//
        //Route::resource('mail_templates', MailTemplates\MailTemplatesController::class);//

        //Route::middleware('group')->group( function () {

        //});
        Route::post('change_request/listCRsUsers', 'ChangeRequest\ChangeRequestController@Crsbyusers');
        Route::get('change_request/listcrsbyuser', 'ChangeRequest\ChangeRequestController@list_crs_by_user');
        Route::get('change_request/export-user-created-crs', 'ChangeRequest\ChangeRequestController@exportUserCreatedCRs')->name('change_request.export_user_created_crs');

        Route::get('change_request/on-hold', 'ChangeRequest\ChangeRequestController@cr_hold_promo')->name('cr_hold');
        Route::resource('change_request', 'ChangeRequest\ChangeRequestController');
        Route::get('change_request2/dvision_manager_cr', 'ChangeRequest\ChangeRequestController@dvision_manager_cr')->name('dvision_manager_cr');
        Route::get('change_request2/cr_pending_cap', 'ChangeRequest\ChangeRequestController@cr_pending_cap')->name('cr_pending_cap');

        Route::get('dvision_manager_cr/unreadNotifications', 'ChangeRequest\ChangeRequestController@unreadNotifications');
        Route::get('change_request1/asd/{group?}', 'ChangeRequest\ChangeRequestController@asd')->name('change_request.asd');
        Route::post('/select-group/{group}', 'ChangeRequest\ChangeRequestController@selectGroup')->name('select_group');


        Route::post('/change-requests/reorder', 'ChangeRequest\ChangeRequestController@reorderChangeRequest')->name("change-requests.reorder");
        Route::post('/change-requests/hold', 'ChangeRequest\ChangeRequestController@holdChangeRequest')->name("change-requests.hold");
        Route::get('/change-requests/reorder/home', 'ChangeRequest\ChangeRequestController@reorderhome')->name("change-requests.reorder.home");

        Route::get('change_request/workflow/type', 'ChangeRequest\ChangeRequestController@Allsubtype');
        Route::get('files/download/{id}','ChangeRequest\ChangeRequestController@download')->name('files.download');
		Route::get('files/delete/{id}','ChangeRequest\ChangeRequestController@deleteFile')->name('files.delete');

        // send mail routes
        //Route::get('manual_email', 'Mail\MailController@index');
        //Route::get('send-mail', 'Mail\MailController@sendMailByTemplate');

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
        Route::get('change_request/{id}/edit_cab' , 'ChangeRequest\ChangeRequestController@edit_cab')->name('edit_cab.cr');

        Route::resource('cab_users', 'CabUser\CabUserController');
        Route::post('cab_user/updateactive', 'CabUser\CabUserController@updateactive');
        Route::get('create_defect/cr_id/{id}', 'Defect\DefectController@Create');
        Route::post('store_defect', 'Defect\DefectController@store');
        Route::get('edit_defect/{id}', 'Defect\DefectController@edit');
        Route::get('defect/files/download/{id}','Defect\DefectController@download');
        Route::patch('defect_update/{id}','Defect\DefectController@update');
        Route::get('defects', 'Defect\DefectController@index');
        Route::get('show_defect/{id}', 'Defect\DefectController@show');
		Route::resource('sla-calculations', Sla\SlaCalculationController::class);
        Route::get('testable_form', 'ChangeRequest\ChangeRequestController@showTestableForm')->name('testable_form');
        Route::post('update_testable', 'ChangeRequest\ChangeRequestController@updateTestableFlag')->name('update_testable');
        Route::get('add_attachments_form', 'ChangeRequest\ChangeRequestController@showAddAttachmentsForm')->name('add_attachments_form');
        Route::post('store_attachments', 'ChangeRequest\ChangeRequestController@storeAttachments')->name('store_attachments');
        Route::resource('prerequisites', Prerequisites\PrerequisitesController::class);
        Route::get('prerequisites/download/{id}', 'Prerequisites\PrerequisitesController@download')->name('prerequisites.download');

        // Final Confirmation Routes
        Route::get('final-confirmation', 'FinalConfirmation\FinalConfirmationController@index')->name('final_confirmation.index');
        Route::post('final-confirmation/submit', 'FinalConfirmation\FinalConfirmationController@submit')->name('final_confirmation.submit');
        // Notification Routes
        Route::resource('notification_templates', NotificationTemplates\NotificationTemplatesController::class);
        Route::get('/get-groups/{status_id}', [SlaCalculationController::class, 'getGroups'])->name('get.groups');

        // KPI Routes
        Route::get('kpis/export', 'KPIs\\KPIController@export')->name('kpis.export');
        Route::get('kpis/get-initiatives', 'KPIs\KPIController@getInitiativesByPillar')->name('kpis.get-initiatives');
        Route::get('kpis/get-sub-initiatives', 'KPIs\KPIController@getSubInitiativesByInitiative')->name('kpis.get-sub-initiatives');
        Route::post('kpis/check-requester-email', 'KPIs\KPIController@checkRequesterEmail')->name('kpis.check-requester-email');
        Route::resource('kpis', KPIs\KPIController::class);
        Route::get('kpis/{kpi}/search-cr', 'KPIs\KPIController@searchChangeRequest')->name('kpis.search-cr');
        Route::post('kpis/{kpi}/attach-cr', 'KPIs\KPIController@attachChangeRequest')->name('kpis.attach-cr');
        Route::delete('kpis/{kpi}/detach-cr/{cr}', 'KPIs\KPIController@detachChangeRequest')->name('kpis.detach-cr');

        // Project Manager KPI Routes
        Route::resource('projects', Project\ProjectController::class);
        Route::post('projects/delete-milestone', 'Project\ProjectController@deleteMilestone')->name('projects.delete-milestone');

        Route::prefix('reports')->group(function () {

            Route::get('/actual-vs-planned', [ReportController::class, 'actualVsPlanned'])
                ->name('reports.actual_vs_planned');

            Route::get('/all-crs-by-requester', [ReportController::class, 'allCrsByRequester'])
                ->name('reports.all_crs_by_requester');

            Route::get('/cr-current-status', [ReportController::class, 'crCurrentStatus'])
                ->name('reports.cr_current_status');

            Route::get('/cr-crossed-sla', [ReportController::class, 'crCrossedSla'])
                ->name('reports.cr_crossed_sla');

            Route::get('/rejected-crs', [ReportController::class, 'rejectedCrs'])
                ->name('reports.rejected_crs');

        });



        Route::post('/reports/cr-current-status', [ReportController::class, 'crCurrentStatus'])
    ->name('report.current-status');
    Route::post('/reports/cr-current-status/export', [ReportController::class, 'exportCurrentStatus'])
    ->name('report.current-status.export');
Route::get('/reports/actual-vs-planned', [ReportController::class, 'actualVsPlanned'])
     ->name('actual.vs.planned');
     Route::post('/reports/all-crs-by-requester/export',
    [ReportController::class, 'exportAllCrsByRequester'])
    ->name('all.crs.by.requester.export');
    Route::get('/report/crs-crossed-sla/export', [ReportController::class, 'exportCrsCrossedSla'])
    ->name('report.cross_sla.export');
    Route::get('/report/rejected-crs/export', [ReportController::class, 'exportRejectedCrs'])
    ->name('report.rejected_crs.export');

    Route::get('/configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
    Route::post('/configurations/update', [ConfigurationController::class, 'update'])->name('configurations.update');

        //test ews

        /*Route::get('/test-ews', function () {
            $emails = app(EwsMailReader::class)->handleApprovals();

            //foreach ($emails as $email) {
            //    echo "<h2>{$email['subject']}</h2>";
            //    echo "<p><strong>From:</strong> {$email['from']}<br><strong>Date:</strong> {$email['date']}</p>";
            //    echo "<div style='padding:10px; border:1px solid #ccc; margin-bottom:15px;'>{$email['body']}</div>";
            //}
        });*/


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
