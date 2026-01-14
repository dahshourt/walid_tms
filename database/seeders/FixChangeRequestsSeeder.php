<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixChangeRequestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Update category_id
        DB::statement("
            UPDATE tms_prod.change_request
            SET category_id = 1
            WHERE cr_no IN (
                '6154','2000','2075','2001','2002','2007','2006','2003','2004','2005',
                '2008','2074','6011','6152','6151','2011','6150','6149','6148','2012',
                '2013','2014','6147','2015','2016','6146','2073','6030','2017','2018',
                '2019','2020','6031','6032','2072','6034','2021','2022','6035','6037',
                '6038','6039','6145','2023','6041','6144','6043','2024','2025','2026',
                '2027','2028','2029','2030','2031','2032','6143','2033','6046','6047',
                '6048','2034','2071','2070','6051','2035','6140','2036','2069','2037',
                '2038','2068','6056','6136','6134','2067','6132','6061','6131','2039',
                '2040','2041','6063','6064','2042','6065','6066','2043','2044','6067',
                '6068','2045','2066','6071','2065','6073','6074','2064','6121','2047',
                '2063','6078','6079','6080','6118','2048','2049','6117','6114','2062',
                '6113','2050','6086','2051','6087','6088','6089','6090','2061','2052',
                '2053','2054','2055','2060','2059','6112','6095','2058','2056','6106',
                '2057','6109'
            );
        ");

        // 2. Update requester_department with CASE statement
        DB::statement("
            UPDATE tms_prod.change_request
            SET requester_department = CASE cr_no
                WHEN '6086' THEN 'Content Mangement'
                WHEN '2032' THEN 'Blank'
                WHEN '2004' THEN 'Blank'
                WHEN '6048' THEN 'Customer Service'
                WHEN '6095' THEN 'Technical Operation'

                WHEN '6027' THEN 'Information Technology'
                WHEN '6012' THEN 'Information Technology'
                WHEN '6011' THEN 'Information Technology'
                WHEN '6010' THEN 'Information Technology'
                WHEN '6029' THEN 'Information Technology'
                WHEN '6030' THEN 'Information Technology'
                WHEN '6058' THEN 'Information Technology'
                WHEN '6074' THEN 'Information Technology'
                WHEN '6015' THEN 'Information Technology'
                WHEN '6024' THEN 'Information Technology'
                WHEN '2020' THEN 'Information Technology'
                WHEN '6043' THEN 'Information Technology'
                WHEN '2024' THEN 'Information Technology'
                WHEN '2025' THEN 'Information Technology'
                WHEN '2033' THEN 'Information Technology'
                WHEN '6046' THEN 'Information Technology'
                WHEN '6047' THEN 'Information Technology'
                WHEN '6054' THEN 'Information Technology'
                WHEN '6081' THEN 'Information Technology'
                WHEN '2055' THEN 'Information Technology'
                WHEN '6111' THEN 'Information Technology'
                WHEN '6121' THEN 'Information Technology'
                WHEN '6136' THEN 'Information Technology'
                WHEN '2068' THEN 'Information Technology'
                WHEN '6050' THEN 'Information Technology'
                WHEN '6124' THEN 'Information Technology'
                WHEN '2001' THEN 'Information Technology'
                WHEN '2002' THEN 'Information Technology'
                WHEN '2007' THEN 'Information Technology'
                WHEN '2011' THEN 'Information Technology'
                WHEN '6025' THEN 'Information Technology'
                WHEN '6092' THEN 'Information Technology'
                WHEN '6056' THEN 'Information Technology'
                WHEN '6084' THEN 'Information Technology'
                WHEN '6085' THEN 'Information Technology'
                WHEN '6108' THEN 'Information Technology'
                WHEN '6110' THEN 'Information Technology'
                WHEN '6125' THEN 'Information Technology'
                WHEN '2041' THEN 'Information Technology'
                WHEN '6122' THEN 'Information Technology'
                WHEN '6075' THEN 'Information Technology'
                WHEN '6076' THEN 'Information Technology'
                WHEN '6077' THEN 'Information Technology'
                WHEN '6082' THEN 'Information Technology'
                WHEN '6123' THEN 'Information Technology'
                WHEN '6126' THEN 'Information Technology'
                WHEN '2012' THEN 'Information Technology'
                WHEN '6116' THEN 'Information Technology'
                WHEN '6049' THEN 'Information Technology'

                WHEN '2023' THEN 'revenue assurance & fraud management'
                WHEN '2038' THEN 'revenue assurance & fraud management'
                WHEN '2034' THEN 'revenue assurance & fraud management'
                WHEN '6070' THEN 'revenue assurance & fraud management'
                WHEN '2022' THEN 'revenue assurance & fraud management'
            END
            WHERE cr_no IN (
                '6086','2032','2004','6048','6095',
                '6027','6012','6011','6010','6029','6030','6058','6074','6015','6024',
                '2020','6043','2024','2025','2033','6046','6047','6054','6081','2055',
                '6111','6121','6136','2068','6050','6124','2001','2002','2007','2011',
                '6025','6092','6056','6084','6085','6108','6110','6125','2041','6122',
                '6075','6076','6077','6082','6123','6126','2012','6116','6049',
                '2023','2038','2034','6070','2022'
            );
        ");

        // 3. Update requester_department from requester_departments table
        DB::statement("
            UPDATE change_request cr
            JOIN requester_departments rd
                ON cr.requester_department = rd.id
            SET cr.requester_department = rd.name
            WHERE cr.cr_no IN (
                6139,6140,6148,2072,2073,2074,2076,2077,2078,
                6112,2070,2071,2075,6052,6055,6120,6129,
                6131,6132,6133,6137,6138,6142,6143,6145,
                6146,6147,6149,6150,6151,6152,6153,6154,
                6155,6156,6157,6158,6159,6160,6161,6162,
                6163,6164,6165,6166,6167,6144
            );
        ");

        // 4. Update change_request_statuses 1
        DB::statement("UPDATE `change_request_statuses` SET `new_status_id` = '19' WHERE (`id` = '926');");

        // 5. Update change_request_statuses 2
        DB::statement("UPDATE `change_request_statuses` SET `new_status_id` = '19' WHERE (`id` = '854');");

        // 6. Insert log 1
        DB::statement("INSERT INTO `logs` (`cr_id`, `user_id`, `log_text`) VALUES ('31064', '435', 'Issue manually set to status \'Rejected\' by Admin');");

        // 7. Insert log 2
        DB::statement("INSERT INTO `logs` (`cr_id`, `user_id`, `log_text`) VALUES ('31054', '127', 'Issue manually set to status \'Rejected\' by Admin');");
    }
}
