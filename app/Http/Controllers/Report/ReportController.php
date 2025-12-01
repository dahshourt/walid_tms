<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Factories\Statuses\StatusFactory;
use App\Factories\NewWorkFlow\NewWorkFlowFactory;
use App\Factories\Workflow\Workflow_type_factory;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ReportController extends Controller
{
        private $status;
        private $workflow;
        private $workflow_type;

     public function __construct(
       
        StatusFactory $status,
        NewWorkFlowFactory $workflow,
       Workflow_type_factory $workflow_type,
    ) {
      
        $this->status = $status::index();
        $this->changerworkflowequeststatus = $workflow::index();
        $this->workflow_type = $workflow_type::index();
      

       // $this->shareViewData();
    }
    /**
     * Show Actual vs Planned report page
     */
   /* public function actualVsPlanned(Request $request)
    {
        $query = "
                      WITH designprogress_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 15
),
 pend_design_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 7
),
    pend_implementaion_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 8
),
technical_implementation_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        group_id,
        user_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 10
),
    pend_testing_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 11
)

   SELECT 
        req.id,
        req.cr_no,
        apps.`name` 'Applications',
        req.title,
        flow.`name` 'CR Type',
        req.start_design_time 'Design Estimation Planned Start',
        req.end_design_time 'Design Estimation Planned End',
        designprogress.created_at AS DesignInProgressActualStart,
        designprogress.updated_at AS DesignInProgressActualEnd,
        TIMESTAMPDIFF(MINUTE,ch_cus_fields.created_at , pendig_disgn_start.created_at ) 'Pending Design Assigned Member Duration IN MINUTES',
        design_team_name.`name` as 'Team Member Name',

        req.start_develop_time 'Technical Estimation Planned Start',
        req.end_develop_time 'Technical Estimation Planned End',
        
        tetch_implt_start.created_at 'TechnicalImplementationActualStart',
        tetch_implt_start.updated_at 'TechnicalImplementationActualEnd',
        TIMESTAMPDIFF(MINUTE,ch_cus_tech_flds.created_at , pend_implement.created_at ) 'Pending Implementation Assigned Member Duration IN MINUTES',
        group_concat(technical_team.title)  'Technical Team',
        pend_imple_assig_usr.`name` 'Pending Implementation Assigned Member',
        req.start_test_time 'Testing Estimation Planned Start',
        req.end_test_time 'Testing Estimation Planned End', 
        pend_test.created_at 'Pending Testing ActualStart',
        pend_test.updated_at 'Pending Testing ActualEnd',
        
        pend_test_assig_usr.`name` 'Testing Team Member',
        IFNULL(req.end_test_time, req.end_develop_time) as 'Expected Delivery date'  ,
        req.requester_name,
        req.division_manager,
        'Not Found' as 'Requseter division',
        'Not Found' as 'Requester Sector'

    FROM  change_request AS req
    LEFT JOIN applications AS apps ON apps.id = req.application_id
    LEFT JOIN workflow_type AS flow ON flow.id = req.workflow_type_id
    LEFT JOIN change_request_statuses AS curr_status  ON curr_status.cr_id = req.id  
    LEFT JOIN statuses AS stat ON stat.id = curr_status.new_status_id 
    
   LEFT JOIN designprogress_ranked designprogress ON designprogress.cr_id = req.id AND designprogress.rn = 1
   LEFT JOIN pend_implementaion_ranked pend_implement ON pend_implement.cr_id = req.id AND pend_implement.rn = 1
   LEFT JOIN pend_testing_ranked pend_test ON pend_test.cr_id = req.id AND pend_test.rn = 1
   LEFT JOIN change_request_custom_fields AS ch_cus_fields ON ch_cus_fields.cr_id = req.id and ch_cus_fields.custom_field_id = 48

   LEFT JOIN pend_design_ranked AS pendig_disgn_start ON pendig_disgn_start.cr_id = req.id  AND pendig_disgn_start.rn = 1
   LEFT JOIN technical_implementation_ranked AS tetch_implt_start ON tetch_implt_start.cr_id = req.id  AND tetch_implt_start.rn = 1
    LEFT JOIN user_groups AS usr_grp ON usr_grp.user_id = tetch_implt_start.user_id 
   LEFT JOIN `groups` AS technical_team ON technical_team.id = usr_grp.group_id 
   LEFT JOIN change_request_custom_fields AS ch_cus_tech_flds ON ch_cus_tech_flds.cr_id = req.id and ch_cus_tech_flds.custom_field_id = 46
   LEFT JOIN users AS design_team_name ON design_team_name.id = ch_cus_fields.custom_field_value
   LEFT JOIN users AS designprogress_assig_usr ON designprogress_assig_usr.id = designprogress.user_id 
   LEFT JOIN users AS pend_imple_assig_usr ON pend_imple_assig_usr.id = pend_implement.user_id 
   LEFT JOIN `groups` AS grop ON grop.id = pend_implement.group_id 
   LEFT JOIN users AS pend_test_assig_usr ON pend_test_assig_usr.id = pend_test.user_id 
  
    GROUP BY req.cr_no;
                ";

                $results = \DB::select($query);
                $results = collect($results);

                // Pagination setup
                $perPage = 10;
                $page = $request->get('page', 1);

                // Slice collection for current page
                $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

                // Create LengthAwarePaginator
                $paginatedResults = new LengthAwarePaginator(
                    $currentPageItems,
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                return view('reports.actual_vs_planned', [
                    'results' => $paginatedResults // <-- pass paginator to view
                ]);
    }*/

public function actualVsPlanned(Request $request)
    {
        $query = DB::table('change_request as req')
            ->leftJoin('applications as apps', 'apps.id', '=', 'req.application_id')
            ->leftJoin('workflow_type as flow', 'flow.id', '=', 'req.workflow_type_id')
            ->leftJoin('change_request_statuses as curr_status', 'curr_status.cr_id', '=', 'req.id')
            ->leftJoin('statuses as stat', 'stat.id', '=', 'curr_status.new_status_id')

            // Join CTE-equivalent tables using DB::raw and subqueries
            ->leftJoin(DB::raw("(SELECT * FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
                    FROM change_request_statuses
                    WHERE new_status_id = 15
                ) AS x WHERE rn = 1) AS designprogress"), "designprogress.cr_id", "=", "req.id")

            ->leftJoin(DB::raw("(SELECT * FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
                    FROM change_request_statuses
                    WHERE new_status_id = 7
                ) AS x WHERE rn = 1) AS pendig_disgn_start"), "pendig_disgn_start.cr_id", "=", "req.id")

            ->leftJoin(DB::raw("(SELECT * FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
                    FROM change_request_statuses
                    WHERE new_status_id = 8
                ) AS x WHERE rn = 1) AS pend_implement"), "pend_implement.cr_id", "=", "req.id")

            ->leftJoin(DB::raw("(SELECT * FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
                    FROM change_request_statuses
                    WHERE new_status_id = 10
                ) AS x WHERE rn = 1) AS tetch_implt_start"), "tetch_implt_start.cr_id", "=", "req.id")

            ->leftJoin(DB::raw("(SELECT * FROM (
                    SELECT *,
                        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
                    FROM change_request_statuses
                    WHERE new_status_id = 11
                ) AS x WHERE rn = 1) AS pend_test"), "pend_test.cr_id", "=", "req.id")

            ->leftJoin('change_request_custom_fields as ch_cus_fields', function ($join) {
                $join->on('ch_cus_fields.cr_id', '=', 'req.id')
                    ->where('ch_cus_fields.custom_field_id', '=', 48);
            })

            ->leftJoin('change_request_custom_fields as ch_cus_tech_flds', function ($join) {
                $join->on('ch_cus_tech_flds.cr_id', '=', 'req.id')
                    ->where('ch_cus_tech_flds.custom_field_id', '=', 46);
            })

            ->leftJoin('users as design_team_name', 'design_team_name.id', '=', 'ch_cus_fields.custom_field_value')
            ->leftJoin('users as pend_imple_assig_usr', 'pend_imple_assig_usr.id', '=', 'pend_implement.user_id')
            ->leftJoin('users as pend_test_assig_usr', 'pend_test_assig_usr.id', '=', 'pend_test.user_id')
            ->leftJoin('user_groups as usr_grp', 'usr_grp.user_id', '=', 'tetch_implt_start.user_id')
            ->leftJoin('groups as technical_team', 'technical_team.id', '=', 'usr_grp.group_id')

            ->selectRaw("
                req.id,
                req.cr_no,
                apps.name AS `Applications`,
                req.title,
                flow.name AS `CR Type`,
                req.start_design_time AS `Design Estimation Planned Start`,
                req.end_design_time AS `Design Estimation Planned End`,
                designprogress.created_at AS DesignInProgressActualStart,
                designprogress.updated_at AS DesignInProgressActualEnd,
                TIMESTAMPDIFF(MINUTE, ch_cus_fields.created_at, pendig_disgn_start.created_at) AS `Pending Design Assigned Member Duration IN MINUTES`,
                design_team_name.name AS `Team Member Name`,
                req.start_develop_time AS `Technical Estimation Planned Start`,
                req.end_develop_time AS `Technical Estimation Planned End`,
                tetch_implt_start.created_at AS `TechnicalImplementationActualStart`,
                tetch_implt_start.updated_at AS `TechnicalImplementationActualEnd`,
                TIMESTAMPDIFF(MINUTE, ch_cus_tech_flds.created_at, pend_implement.created_at) AS `Pending Implementation Assigned Member Duration IN MINUTES`,
                GROUP_CONCAT(technical_team.title) AS `Technical Team`,
                pend_imple_assig_usr.name AS `Pending Implementation Assigned Member`,
                req.start_test_time AS `Testing Estimation Planned Start`,
                req.end_test_time AS `Testing Estimation Planned End`,
                pend_test.created_at AS `Pending Testing ActualStart`,
                pend_test.updated_at AS `Pending Testing ActualEnd`,
                pend_test_assig_usr.name AS `Testing Team Member`,
                IFNULL(req.end_test_time, req.end_develop_time) AS `Expected Delivery date`,
                req.requester_name,
                req.division_manager,
                'Not Found' AS `Requseter division`,
                'Not Found' AS `Requester Sector`
            ")
            ->groupBy("req.cr_no");

        // handle export
        if ($request->has('export')) {
            return Excel::download(new ActualVsPlannedReportExport($query->get()), 'actual_vs_planned.xlsx');
        }

        // paginate
        $results = $query->paginate(10);

        return view('reports.actual_vs_planned', compact('results'));
    }


    /**
     * Show All CRs By Requester report page
     */
    public function allCrsByRequester(Request $request)
    {
$query = "
                       WITH pend_design_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 7
),
    pend_implementaion_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 8
),
    pend_testing_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 11
),
--     review_estimate_ranked AS (
--     SELECT 
--         cr_id,
--         id,
--         created_at,
--         updated_at,
--         ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
--     FROM change_request_statuses
--     WHERE new_status_id = 70
-- ),
    busins_val_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 18
),
    sanity_check_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 21
),
    delivred_cr_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 27
)


   SELECT 
        req.id,
        req.cr_no,
        apps.`name` 'Applications',
        req.title,
        flow.`name` 'Workflow Type',
        'Not Found' as 'CR Type',
        'NA' as 'Vendor Name',
         GROUP_CONCAT(DISTINCT stat.status_name ORDER BY stat.status_name SEPARATOR ', ') AS 'Current Status',
--        review_estimate.created_at 'Review And Estimation Start',
--        review_estimate.updated_at 'Review And Estimation End',
        busins_val.created_at 'Business Validation Status Start Date',
        busins_val.updated_at 'Business Validation Status End Date',
    --    'Not Found' as 'CAB Review Start',
    --    'Not Found'as 'CAB Review End',
        'Not Found' as 'Design Assigned Member Level',

        req.start_design_time 'Pending Design Planned Start',
        req.end_design_time 'Pending Design Planned End',
        pend_design.created_at AS PendingDesignActualStart,
        pend_design.updated_at AS PendingDesignActualEnd,
        pend_design_assig_usr.`name` 'Pending Design Assigned Member',

        req.start_develop_time 'Technical Estimation Start Date',
        req.end_develop_time 'Technical Estimation End Date',
        pend_implement.created_at 'PendingImplementationActualStart',
        pend_implement.updated_at 'PendingImplementationActualEnd',
        pend_imple_assig_usr.`name` 'Developer Name',
        grop.title 'Technical Team',
        TIMESTAMPDIFF(MINUTE,ch_cus_fields.created_at , pend_implement.created_at ) 'Pending Design Assigned Member Duration IN MINUTES',
        'Not Found' as 'Dev Assigned Member Level',

        req.start_test_time 'Testing Estimation Start Date',
        req.end_test_time 'Testing Estimation End Date',
        pend_test.created_at 'Pending Testing Start',
        pend_test.updated_at 'Pending Testing End',
        TIMESTAMPDIFF(MINUTE,ch_cus_fields_tst.created_at , pend_test.created_at ) 'Pending Design Assigned Member Duration IN MINUTES',
        pend_test_assig_usr.`name` 'Testing Team Member',
        'Not Found' as 'Testing Assigned Member Level',

        chang_stat_pend_prod_deploy.created_at 'Pending Production Deployment Start Date',
        chang_stat_pend_prod_deploy.updated_at 'Pending Production Deployment End Date',
        sanity_check.created_at 'Sanity Check Start',
        sanity_check.updated_at 'Sanity Check End',
        chang_stat_pend_busen_fedbk.created_at 'Pending Business Feedback Start Date',
        chang_stat_pend_busen_fedbk.updated_at 'Pending Business Feedback End Date',
        chang_stat_busen_tst_cas_appval.created_at 'Business Test Case Approval Start Date',
        chang_stat_busen_tst_cas_appval.updated_at 'Business Test Case Approval End Date',
        chang_stat_busen_uat_sign_off.created_at 'Business UAT Sign Off Start Date',
        chang_stat_busen_uat_sign_off.updated_at 'Business UAT Sign Off End Date',
        delivred_cr.created_at 'Delivered CR Start',
        delivred_cr.updated_at 'Delivered CR Start',

  --      'Not Found' as 'Deployment on production',
        IFNULL(req.end_test_time, req.end_develop_time) as 'Expected Delivery date',
        req.requester_name,
        req.division_manager,
        'Not Found' as 'Requseter division',
        'Not Found' as 'Requester Sector',
         rejt_reason.name 'Rejection Reasons'

    FROM  change_request AS req
    LEFT JOIN applications AS apps ON apps.id = req.application_id
    LEFT JOIN workflow_type AS flow ON flow.id = req.workflow_type_id
    -- LEFT JOIN change_request_statuses AS curr_status  ON curr_status.cr_id = req.id  
    -- LEFT JOIN statuses AS stat ON stat.id = curr_status.new_status_id 
    
    LEFT JOIN change_request_statuses AS curr_status ON curr_status.cr_id = req.id AND curr_status.`active` = '1'
    LEFT JOIN statuses AS stat ON stat.id = curr_status.new_status_id

    LEFT JOIN pend_design_ranked pend_design ON pend_design.cr_id = req.id AND pend_design.rn = 1
    LEFT JOIN pend_implementaion_ranked pend_implement ON pend_implement.cr_id = req.id AND pend_implement.rn = 1
    LEFT JOIN pend_testing_ranked pend_test ON pend_test.cr_id = req.id AND pend_test.rn = 1
 --   LEFT JOIN review_estimate_ranked review_estimate ON review_estimate.cr_id = req.id AND review_estimate.rn = 1
    LEFT JOIN busins_val_ranked busins_val ON busins_val.cr_id = req.id AND busins_val.rn = 1
    LEFT JOIN sanity_check_ranked sanity_check ON sanity_check.cr_id = req.id AND sanity_check.rn = 1
    LEFT JOIN delivred_cr_ranked delivred_cr ON delivred_cr.cr_id = req.id AND delivred_cr.rn = 1

  --  LEFT JOIN pend_design_ranked AS pendig_disgn_start ON pendig_disgn_start.cr_id = req.id  AND pendig_disgn_start.rn = 1
    LEFT JOIN change_request_custom_fields AS ch_cus_fields ON ch_cus_fields.cr_id = req.id and ch_cus_fields.custom_field_id = 46
    LEFT JOIN change_request_custom_fields AS ch_cus_fields_tst ON ch_cus_fields_tst.cr_id = req.id and ch_cus_fields_tst.custom_field_id = 47

    LEFT JOIN change_request_statuses as chang_stat_pend_prod_deploy ON  chang_stat_pend_prod_deploy.cr_id = req.id and  chang_stat_pend_prod_deploy.new_status_id = 17
    LEFT JOIN change_request_statuses as chang_stat_pend_desin ON  chang_stat_pend_desin.cr_id = req.id and  chang_stat_pend_desin.new_status_id = 7
    LEFT JOIN change_request_statuses as chang_stat_pend_busen_fedbk ON  chang_stat_pend_busen_fedbk.cr_id = req.id and  chang_stat_pend_busen_fedbk.new_status_id = 79
    LEFT JOIN change_request_statuses as chang_stat_busen_tst_cas_appval ON  chang_stat_busen_tst_cas_appval.cr_id = req.id and  chang_stat_busen_tst_cas_appval.new_status_id = 41
    LEFT JOIN change_request_statuses as chang_stat_busen_uat_sign_off ON  chang_stat_busen_uat_sign_off.cr_id = req.id and  chang_stat_busen_uat_sign_off.new_status_id = 44

    LEFT JOIN users AS pend_design_assig_usr ON pend_design_assig_usr.id = pend_design.user_id 
    LEFT JOIN users AS pend_imple_assig_usr ON pend_imple_assig_usr.id = pend_implement.user_id 
    LEFT JOIN `groups` AS grop ON grop.id = pend_implement.group_id 
    LEFT JOIN users AS pend_test_assig_usr ON pend_test_assig_usr.id = pend_test.user_id 
    LEFT JOIN change_request_custom_fields as chang_custm_rejt_reason ON  chang_custm_rejt_reason.cr_id = req.id and  chang_custm_rejt_reason.custom_field_id = 63
    LEFT JOIN rejection_reasons as rejt_reason ON  rejt_reason.id = chang_custm_rejt_reason.custom_field_value 
   
   GROUP BY req.cr_no;

                ";

                $results = \DB::select($query);
                $results = collect($results);

                // Pagination setup
                $perPage = 10;
                $page = $request->get('page', 1);

                // Slice collection for current page
                $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

                // Create LengthAwarePaginator
                $paginatedResults = new LengthAwarePaginator(
                    $currentPageItems,
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                return view('reports.all_crs_by_requester', [
                    'results' => $paginatedResults // <-- pass paginator to view
                ]);

        
    }

    public function exportAllCrsByRequester()
        {
            return Excel::download(new AllCrsByRequesterExport, 'all_crs_by_requester.xlsx');
        }

    /**
     * Show CR Current Status report page
     */
//   public function crCurrentStatus(Request $request)
// {
//      /* ---------------------------------------------------------
//         1) READ FILTERS FROM FORM
//     --------------------------------------------------------- */
//     $cr_type = $request->input('cr_type');                     // single value
//     $status_ids = $request->input('status_ids');               // array
//     $cr_nos = $request->input('cr_nos');                       // optional text field: "1001,1002"
   
//     //dd(intval($cr_type));
//     // Convert arrays to comma-separated strings
//     $status_ids_str = !empty($status_ids) ? implode(",", $status_ids) : null;

//     /* ---------------------------------------------------------
//         2) SET MYSQL USER VARIABLES TO PASS INTO THE QUERY
//     --------------------------------------------------------- */
//     DB::statement("SET @cr_type := " . ($cr_type ? $cr_type : "NULL"));
//     DB::statement("SET @status_ids := " . ($status_ids_str ? "'" . $status_ids_str . "'" : "NULL"));
//     DB::statement("SET @cr_nos := " . ($cr_nos ? "'" . $cr_nos . "'" : "NULL"));


//     // Main SELECT query (cleaned & corrected)
//     $query = "
//         SELECT 
//             req.cr_no,
//             apps.name AS `Applications`,
//             req.title,
//             flow.name AS `Workflow Type`,
//             'NA' AS `Vendor Name`,
//             GROUP_CONCAT(DISTINCT stat.status_name ORDER BY stat.status_name SEPARATOR ', ') AS `Current Status`,
//             CONCAT(sla.unit_sla_time, ' ', sla.sla_type_unit) AS `Assigned SLA`,
//             req.start_design_time AS `Design Estimation Start`,
//             req.end_design_time AS `Design Estimation End`,
//             req.start_develop_time AS `Technical Estimation Start`,
//             req.end_develop_time AS `Technical Estimation End`,
//             unt.name AS `Unit Name`,
//             req.start_test_time AS `Testing Estimation Start`,
//             req.end_test_time AS `Testing Estimation End`,
//             grou.title AS `Current Assigned Group`,
//             usr.user_name AS `Assigned Member`,
//             'Not Found' AS `Assigned Member Level`,
//             IFNULL(req.end_test_time, req.end_develop_time) AS `Expected Delivery date`,
//             req.requester_name,
//             req.division_manager
//         FROM change_request AS req
//         LEFT JOIN applications AS apps 
//             ON apps.id = req.application_id
//         LEFT JOIN workflow_type AS flow 
//             ON flow.id = req.workflow_type_id
//         LEFT JOIN change_request_statuses AS curr_status 
//             ON curr_status.cr_id = req.id 
//             AND curr_status.active = '1'
//         LEFT JOIN statuses AS stat 
//             ON stat.id = curr_status.new_status_id
//         LEFT JOIN group_statuses AS gro_stat 
//             ON gro_stat.status_id = curr_status.new_status_id
//         LEFT JOIN `groups` AS grou 
//             ON grou.id = gro_stat.group_id
//         LEFT JOIN group_applications AS grou_apps 
//             ON grou_apps.application_id = req.application_id
//         LEFT JOIN `groups` AS grou_unit 
//             ON grou_unit.id = grou_apps.group_id
//         LEFT JOIN units AS unt 
//             ON unt.id = grou_unit.unit_id
//         LEFT JOIN sla_calculations AS sla 
//             ON sla.status_id = curr_status.new_status_id
//         LEFT JOIN change_request_custom_fields AS custom_field_chang 
//             ON custom_field_chang.cr_id = req.id 
//             AND custom_field_chang.custom_field_id = 67
//         LEFT JOIN users AS usr 
//             ON usr.id = custom_field_chang.custom_field_value 
//         LEFT JOIN roles  
//             ON roles.id = usr.role_id
//         WHERE
//             (@cr_type IS NULL OR req.workflow_type_id = @cr_type)
//             AND (@status_ids IS NULL OR FIND_IN_SET(curr_status.new_status_id, @status_ids))
//             AND (@cr_nos IS NULL OR FIND_IN_SET(req.cr_no, @cr_nos))
//         GROUP BY req.cr_no
//     ";

//     // Execute query
//     $results = collect(DB::select($query));

//     // Pagination
//     $perPage = 10;
//     $page = $request->get('page', 1);

//     $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

//     $paginatedResults = new LengthAwarePaginator(
//         $currentPageItems,
//         $results->count(),
//         $perPage,
//         $page,
//         [
//             'path' => $request->url(),
//             'query' => $request->query()
//         ]
//     );
//     $workflow_types =$this->workflow_type->get_workflow_all_subtype();
//     $status =$this->status->getAll();
   
//     return view('reports.cr_current_status', [
//         'results' => $paginatedResults,
//         'status' => $status,
//         'workflow_type' => $workflow_types
//     ]);
// }

    public function crCurrentStatus(Request $request)
    {
        // 1) Read filters from form (optional)
        $cr_type = $request->input('cr_type');                // single value
        $status_ids = $request->input('status_ids', []);      // array
        $cr_nos = $request->input('cr_nos');                 // optional text field "CR001,CR002"

        // 2) Build query dynamically
        $query = DB::table('change_request as req')
            ->leftJoin('applications as apps', 'apps.id', '=', 'req.application_id')
            ->leftJoin('workflow_type as flow', 'flow.id', '=', 'req.workflow_type_id')
            ->leftJoin('change_request_statuses as curr_status', function ($join) {
                $join->on('curr_status.cr_id', '=', 'req.id')
                     ->where('curr_status.active', 1);
            })
            ->leftJoin('statuses as stat', 'stat.id', '=', 'curr_status.new_status_id')
            ->leftJoin('group_statuses as gro_stat', 'gro_stat.status_id', '=', 'curr_status.new_status_id')
            ->leftJoin('groups as grou', 'grou.id', '=', 'gro_stat.group_id')
            ->leftJoin('group_applications as grou_apps', 'grou_apps.application_id', '=', 'req.application_id')
            ->leftJoin('groups as grou_unit', 'grou_unit.id', '=', 'grou_apps.group_id')
            ->leftJoin('units as unt', 'unt.id', '=', 'grou_unit.unit_id')
            ->leftJoin('sla_calculations as sla', 'sla.status_id', '=', 'curr_status.new_status_id')
            ->leftJoin('change_request_custom_fields as custom_field_chang', function($join) {
                $join->on('custom_field_chang.cr_id', '=', 'req.id')
                     ->where('custom_field_chang.custom_field_id', 67);
            })
            ->leftJoin('users as usr', 'usr.id', '=', 'custom_field_chang.custom_field_value')
            ->leftJoin('roles', 'roles.id', '=', 'usr.role_id')
            ->select(
                'req.cr_no',
                'apps.name as Applications',
                'req.title',
                'flow.name as Workflow_Type',
                DB::raw("'NA' as Vendor_Name"),
                DB::raw("GROUP_CONCAT(DISTINCT stat.status_name ORDER BY stat.status_name SEPARATOR ', ') as Current_Status"),
                DB::raw("CONCAT(sla.unit_sla_time, ' ', sla.sla_type_unit) as Assigned_SLA"),
                'req.start_design_time as Design_Estimation_Start',
                'req.end_design_time as Design_Estimation_End',
                'req.start_develop_time as Technical_Estimation_Start',
                'req.end_develop_time as Technical_Estimation_End',
                'unt.name as Unit_Name',
                'req.start_test_time as Testing_Estimation_Start',
                'req.end_test_time as Testing_Estimation_End',
                'grou.title as Current_Assigned_Group',
                'usr.user_name as Assigned_Member',
                DB::raw("'Not Found' as Assigned_Member_Level"),
                DB::raw("IFNULL(req.end_test_time, req.end_develop_time) as Expected_Delivery_date"),
                'req.requester_name',
                'req.division_manager'
            )
            ->groupBy('req.cr_no');

        // 3) Apply filters if present
        if($cr_type) {
            $query->where('req.workflow_type_id', $cr_type);
        }

        if(!empty($status_ids)) {
            $query->whereIn('curr_status.new_status_id', $status_ids);
        }

        if($cr_nos) {
            $cr_nos_array = array_map('trim', explode(',', $cr_nos));
            $query->whereIn('req.cr_no', $cr_nos_array);
        }

        $results = collect($query->get());

        // 4) Pagination
        $perPage = 10;
        $page = $request->input('page', 1);
        $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();
        $paginatedResults = new LengthAwarePaginator(
            $currentPageItems,
            $results->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // 5) Get filter options
        $workflow_types = $this->workflow_type->get_workflow_all_subtype();
        $status = $this->status->getAll();

        return view('reports.cr_current_status', [
            'results' => $paginatedResults,
            'workflow_type' => $workflow_types,
            'status' => $status
        ]);
    }
    /**
     * Show CR Crossed SLA report page
     */
    public function crCrossedSla(Request $request)
    {

        $query = "
                         WITH pend_design_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 7
),
    pend_implementaion_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 8
),
    pend_testing_ranked AS (
    SELECT 
        cr_id,
        id,
        created_at,
        updated_at,
        user_id,
        group_id,
        ROW_NUMBER() OVER (PARTITION BY cr_id ORDER BY id DESC) AS rn
    FROM change_request_statuses
    WHERE new_status_id = 11
)

   SELECT 
        req.id,
        req.cr_no,
        apps.`name` 'Applications',
        req.title,
        flow.`name` 'Workflow Type',
        req.start_design_time 'Pending Design Planned Start',
        req.end_design_time 'Pending Design Planned End',
        pend_design.created_at AS PendingDesignActualStart,
        pend_design.updated_at AS PendingDesignActualEnd,
        pend_design_assig_usr.`name` 'Pending Design Assigned Member',
        req.start_develop_time 'Pending Implementation Planned Start',
        req.end_develop_time 'Pending Implementation Planned End',
        pend_implement.created_at 'PendingImplementationActualStart',
        pend_implement.updated_at 'PendingImplementationActualEnd',
        pend_imple_assig_usr.`name` 'Pending Implementation Assigned Member',
        grop.title 'Assigned Group',
        req.start_test_time 'Pending Testing Planned Start',
        req.end_test_time 'Pending Testing Planned End',
        pend_test.created_at 'Pending Testing ActualStart',
        pend_test.updated_at 'Pending Testing ActualEnd',
        pend_test_assig_usr.`name` 'Pending Testing Assigned Member',
        'Not Found' as 'CR Planned Delivery date'  ,
         IFNULL(req.end_test_time, req.end_develop_time) as 'Expected Delivery date',
        req.requester_name,
        req.division_manager,
        'Not Found' as 'Requseter division',
        'Not Found' as 'Requester Sector'

    FROM  change_request AS req
    LEFT JOIN applications AS apps ON apps.id = req.application_id
    LEFT JOIN workflow_type AS flow ON flow.id = req.workflow_type_id
    LEFT JOIN change_request_statuses AS curr_status  ON curr_status.cr_id = req.id  
    LEFT JOIN statuses AS stat ON stat.id = curr_status.new_status_id 
    
   LEFT JOIN pend_design_ranked pend_design ON pend_design.cr_id = req.id AND pend_design.rn = 1
   LEFT JOIN pend_implementaion_ranked pend_implement ON pend_implement.cr_id = req.id AND pend_implement.rn = 1
   LEFT JOIN pend_testing_ranked pend_test ON pend_test.cr_id = req.id AND pend_test.rn = 1
     
   LEFT JOIN users AS pend_design_assig_usr ON pend_design_assig_usr.id = pend_design.user_id 
   LEFT JOIN users AS pend_imple_assig_usr ON pend_imple_assig_usr.id = pend_implement.user_id 
   LEFT JOIN `groups` AS grop ON grop.id = pend_implement.group_id 
   LEFT JOIN users AS pend_test_assig_usr ON pend_test_assig_usr.id = pend_test.user_id 
   
   where 
	 -- Design mismatch
    req.start_design_time < pend_design.created_at
    OR req.end_design_time < pend_design.updated_at
    
    -- Implementation mismatch
    OR req.start_develop_time < pend_implement.created_at
    OR req.end_develop_time < pend_implement.updated_at

    -- Testing mismatch
    OR req.start_test_time < pend_test.created_at
    OR req.end_test_time < pend_test.updated_at
    
    GROUP BY req.cr_no;
    


                ";

                $results = \DB::select($query);
                $results = collect($results);

                // Pagination setup
                $perPage = 10;
                $page = $request->get('page', 1);

                // Slice collection for current page
                $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

                // Create LengthAwarePaginator
                $paginatedResults = new LengthAwarePaginator(
                    $currentPageItems,
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                return view('reports.cr_crossed_sla', [
                    'results' => $paginatedResults // <-- pass paginator to view
                ]);

 
    }


    public function exportCrsCrossedSla()
        {
            return Excel::download(new CRsCrossedSLAExport, 'CRsCrossedSLA.xlsx');
        }

    /**
     * Show Rejected CRs report page
     */
    public function rejectedCrs(Request $request)
    {

   $query = "
           SELECT 
        req.cr_no,
        req.id,
        apps.`name` 'Applications',
        req.title,
        flow.`name` 'CR Type',
   --     chang_stat_reject.created_at 'Review and estimation start date' ,
    --    chang_stat_reject.updated_at 'Review and estimation start date' ,
     --   chang_stat_analysis.created_at 'Analysis start date',
   --     chang_stat_analysis.updated_at 'Analysis start date',
        chang_stat_busin_valid.created_at 'Business Validation Status Start Date',
        chang_stat_busin_valid.updated_at 'Business Validation Status End Date',
        chang_stat_pend_cab.created_at 'Pending CAB status Start Date',
        chang_stat_pend_cab.updated_at 'Pending CAB status End Date',
        chang_stat_designin_prog.created_at 'Design in progress Start date',
        chang_stat_designin_prog.updated_at 'Design in progress End date',
        usr_design.user_name 'Assigned Design Team Member',
        'Not Found'  as 'Team Member Level',
  --      chang_stat_pend_desin.created_at 'pending design Actual Start date',
    --    chang_stat_pend_desin.updated_at 'pending design Actual End date',
        chang_stat_pend_implememt.created_at 'Technical Implementation Start Date',
        chang_stat_pend_implememt.updated_at 'Technical Implementation End Date',
        chang_stat_pend_implememt.group_id 'Assigned Group',
        usr_dev.user_name 'Assigned Dev User',
        'Not Found' as  'Assigned  Dev User Level',
        chang_stat_pend_test.created_at 'Pending Testing Start Date',
        chang_stat_pend_test.updated_at 'Pending Testing End Date',
        usr_test.user_name 'Testing Team Member',
        'Not Found' as 'Assigned  Test User Level',
        chang_stat_pend_prod_deploy.created_at 'Pending Production Deployment Start Date',
        chang_stat_pend_prod_deploy.updated_at 'Pending Production Deployment End Date',
        chang_stat_pend_busen_fedbk.created_at 'Pending Business Feedback Start Date',
        chang_stat_pend_busen_fedbk.updated_at 'Pending Business Feedback End Date',
        chang_stat_busen_tst_cas_appval.created_at 'Business Test Case Approval Start Date',
        chang_stat_busen_tst_cas_appval.updated_at 'Business Test Case Approval End Date',
        chang_stat_busen_uat_sign_off.created_at 'Business UAT Sign Off Start Date',
        chang_stat_busen_uat_sign_off.updated_at 'Business UAT Sign Off End Date',
        
        chang_stat_delivered.created_at 'Delivered Date Start',
        chang_stat_delivered.updated_at 'Delivered Date End',
    --    chang_stat_delivery.created_at 'Release Plan Delivery Date Review',
        IFNULL(req.end_test_time, req.end_develop_time) as 'Expected Delivery date'  ,
         chang_stat_closed.created_at 'Closed Date',
        stat.status_name 'Previous Status',
    --    CONCAT(sla.unit_sla_time, ' ', sla.sla_type_unit) AS `Assigned SLA`,
   --     grou.title AS `Assigned team`,
   --     usr.user_name as 'Assigned Member',
    --    roles.`name` as 'Assigned member level',
        req.requester_name,
        req.division_manager,
        'Not Found' as 'Requseter division',
        'Not Found' as 'Requester Sector',
        rejt_reason.name 'Rejection Reasons'
    FROM  change_request AS req
    LEFT JOIN applications AS apps ON apps.id = req.application_id
    LEFT JOIN workflow_type AS flow ON flow.id = req.workflow_type_id
    LEFT JOIN change_request_statuses AS curr_status 
           ON curr_status.cr_id = req.id 
    LEFT JOIN statuses AS stat ON stat.id = curr_status.old_status_id 
    LEFT JOIN group_statuses AS gro_stat ON gro_stat.status_id = curr_status.new_status_id
 --   LEFT JOIN `groups` AS grou ON grou.id = gro_stat.group_id
    LEFT JOIN sla_calculations as sla ON sla.status_id = curr_status.new_status_id
    LEFT JOIN change_request_custom_fields as custom_field_chang ON custom_field_chang.cr_id = req.id and custom_field_chang.custom_field_id = 67
    LEFT JOIN users as usr ON usr.id = custom_field_chang.custom_field_value 
    LEFT JOIN roles  ON roles.id = usr.role_id 

 --   LEFT JOIN change_request_statuses as chang_stat_reject ON  chang_stat_reject.cr_id = req.id and  chang_stat_reject.new_status_id = 70
 --   LEFT JOIN change_request_statuses as chang_stat_analysis ON  chang_stat_analysis.cr_id = req.id and  chang_stat_analysis.new_status_id = 63
    LEFT JOIN change_request_statuses as chang_stat_busin_valid ON  chang_stat_busin_valid.cr_id = req.id and  chang_stat_busin_valid.new_status_id = 18
    LEFT JOIN change_request_statuses as chang_stat_pend_cab ON  chang_stat_pend_cab.cr_id = req.id and  chang_stat_pend_cab.new_status_id = 38
    LEFT JOIN change_request_statuses as chang_stat_designin_prog ON  chang_stat_designin_prog.cr_id = req.id and  chang_stat_designin_prog.new_status_id = 15
    LEFT JOIN change_request_statuses as chang_stat_pend_prod_deploy ON  chang_stat_pend_prod_deploy.cr_id = req.id and  chang_stat_pend_prod_deploy.new_status_id = 17
    LEFT JOIN change_request_statuses as chang_stat_pend_desin ON  chang_stat_pend_desin.cr_id = req.id and  chang_stat_pend_desin.new_status_id = 7
    LEFT JOIN change_request_statuses as chang_stat_pend_busen_fedbk ON  chang_stat_pend_busen_fedbk.cr_id = req.id and  chang_stat_pend_busen_fedbk.new_status_id = 79
    LEFT JOIN change_request_statuses as chang_stat_busen_tst_cas_appval ON  chang_stat_busen_tst_cas_appval.cr_id = req.id and  chang_stat_busen_tst_cas_appval.new_status_id = 41
    LEFT JOIN change_request_statuses as chang_stat_busen_uat_sign_off ON  chang_stat_busen_uat_sign_off.cr_id = req.id and  chang_stat_busen_uat_sign_off.new_status_id = 44
    LEFT JOIN users as usr_design ON usr_design.id = chang_stat_pend_desin.user_id 
    LEFT JOIN roles as assigned_user_level_design  ON roles.id = usr_design.role_id 

    LEFT JOIN change_request_statuses as chang_stat_pend_implememt ON  chang_stat_pend_implememt.cr_id = req.id and  chang_stat_pend_implememt.new_status_id = 8
    LEFT JOIN users as usr_dev ON usr_dev.id = chang_stat_pend_implememt.user_id 
    LEFT JOIN roles as assigned_user_level_dev  ON roles.id = usr_dev.role_id 

    LEFT JOIN change_request_statuses as chang_stat_pend_test ON  chang_stat_pend_test.cr_id = req.id and  chang_stat_pend_test.new_status_id = 11
    LEFT JOIN users as usr_test ON usr_test.id = chang_stat_pend_test.user_id 
    LEFT JOIN roles as assigned_user_level_test  ON roles.id = usr_test.role_id 


     LEFT JOIN change_request_statuses as chang_stat_closed ON  chang_stat_closed.cr_id = req.id and  chang_stat_closed.new_status_id = 49
  LEFT JOIN change_request_statuses as chang_stat_delivered ON  chang_stat_delivered.cr_id = req.id and  chang_stat_delivered.new_status_id = 27
     LEFT JOIN change_request_statuses as chang_stat_delivery ON  chang_stat_delivery.cr_id = req.id and  chang_stat_delivery.new_status_id = 60
     LEFT JOIN change_request_custom_fields as chang_custm_rejt_reason ON  chang_custm_rejt_reason.cr_id = req.id and  chang_custm_rejt_reason.custom_field_id = 63
     LEFT JOIN rejection_reasons as rejt_reason ON  rejt_reason.id = chang_custm_rejt_reason.custom_field_value 


    where curr_status.new_status_id = 19
    GROUP BY req.cr_no;


                ";

                $results = \DB::select($query);
                $results = collect($results);

                // Pagination setup
                $perPage = 10;
                $page = $request->get('page', 1);

                // Slice collection for current page
                $currentPageItems = $results->slice(($page - 1) * $perPage, $perPage)->values();

                // Create LengthAwarePaginator
                $paginatedResults = new LengthAwarePaginator(
                    $currentPageItems,
                    $results->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                return view('reports.rejected_crs', [
                    'results' => $paginatedResults // <-- pass paginator to view
                ]);   
    }

    public function exportRejectedCrs()
        {
            return Excel::download(new RejectedCRsExport, 'RejectedCRs.xlsx');
        }

    public function exportCurrentStatus(Request $request)
    {
        // Get same filters from POST
        $filters = $request->only(['cr_type', 'status_ids', 'cr_nos']);

        return Excel::download(new TableExport($filters), 'current_status.xlsx');
    }
}
