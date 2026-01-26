<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Update category_id for specific CRs
        $crNosCategory = [
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
        ];

        DB::table('change_request')
            ->whereIn('cr_no', $crNosCategory)
            ->update(['category_id' => 1]);

        $this->command->info('Updated category_id to 1 for specified CRs.');

        // 2. Requester Departments Normalization
        
        // Fix typo: Content Mangement → Content Management
        DB::table('requester_departments')
            ->where('name', 'Content Mangement')
            ->update(['name' => 'Content Management']);

        // Fix singular/plural: Technical Operation → Technical Operations
        DB::table('requester_departments')
            ->where('name', 'Technical Operation')
            ->update(['name' => 'Technical Operations']);

        // Normalize Information Technology (case & spaces)
        DB::statement("UPDATE requester_departments SET name = 'Information Technology' WHERE LOWER(TRIM(name)) = 'information technology'");

        // Normalize Customer Service
        DB::statement("UPDATE requester_departments SET name = 'Customer Service' WHERE LOWER(TRIM(name)) = 'customer service'");

        // Normalize Revenue Assurance & Fraud Management
        DB::statement("UPDATE requester_departments SET name = 'Revenue Assurance & Fraud Management' WHERE LOWER(TRIM(name)) = 'revenue assurance & fraud management'");

        // Remove invalid "Blank" records
        DB::table('requester_departments')
            ->where('name', 'Blank')
            ->orWhereRaw("TRIM(name) = ''")
            ->delete();

        // Insert missing 'Revenue Assurance & Fraud Management' if not exists
        $exists = DB::table('requester_departments')
            ->where('name', 'Revenue Assurance & Fraud Management')
            ->exists();
        
        if (!$exists) {
            DB::table('requester_departments')->insert(['name' => 'Revenue Assurance & Fraud Management']);
        }

        $this->command->info('Normalized requester_departments table.');

        // 3. Update change_request_custom_fields based on CR Logic
        $updateCustomFieldsSql = "
            UPDATE change_request_custom_fields ccf
            JOIN change_request cr
              ON cr.id = ccf.cr_id
            JOIN requester_departments rd
              ON rd.name = CASE
                  WHEN cr.cr_no = 6086 THEN 'Content Management'
                  WHEN cr.cr_no = 6048 THEN 'Customer Service'
                  WHEN cr.cr_no = 6095 THEN 'Technical Operations'
                  WHEN cr.cr_no IN (
                    6027,6012,6011,6010,6029,6030,6058,6074,6015,6024,
                    2020,6043,2024,2025,2033,6046,6047,6054,6081,2055,
                    6111,6121,6136,2068,6050,6124,2001,2002,2007,2011,
                    6025,6092,6056,6084,6085,6108,6110,6125,2041,6122,
                    6075,6076,6077,6082,6123,6126,2012,6116,6049
                  ) THEN 'Information Technology'
                  WHEN cr.cr_no IN (
                    2023,2038,2034,6070,2022
                  ) THEN 'Revenue Assurance & Fraud Management'
                  ELSE NULL
              END
            SET ccf.custom_field_value = rd.id
            WHERE ccf.custom_field_id = 29
              AND cr.cr_no IN (
                6086,2032,2004,6048,6095,6027,6012,6011,6010,6029,6030,
                6058,6074,6015,6024,2020,6043,2024,2025,2033,6046,6047,
                6054,6081,2055,6111,6121,6136,2068,6050,6124,2001,2002,
                2007,2011,6025,6092,6056,6084,6085,6108,6110,6125,2041,
                6122,6075,6076,6077,6082,6123,6126,2023,2038,2034,
                6070,2022,2012,6116,6049
              )
        ";
        DB::statement($updateCustomFieldsSql);
        $this->command->info('Updated change_request_custom_fields values.');

        // 4. Update change_request.requester_department to name
        $updateCrDepartmentSql = "
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
            )
        ";
        DB::statement($updateCrDepartmentSql);
        $this->command->info('Updated change_request table requester_department names.');

        // 5. Update Statuses
        DB::table('change_request_statuses')
            ->where('id', '926')
            ->update(['new_status_id' => '19']);
            
        DB::table('change_request_statuses')
            ->where('id', '854')
            ->update(['new_status_id' => '19']);
        
        $this->command->info('Updated change_request_statuses.');

        // 6. Insert Logs
        DB::table('logs')->insert([
            ['cr_id' => '31064', 'user_id' => '435', 'log_text' => "Issue manually set to status 'Rejected' by Admin", 'created_at' => now(), 'updated_at' => now()],
            ['cr_id' => '31054', 'user_id' => '127', 'log_text' => "Issue manually set to status 'Rejected' by Admin", 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('Inserted manual logs.');
    }
}
