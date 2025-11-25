<?php

return [
    'cairo' =>
        [    
            'ldap_host' 	=> 'Cairo.TelecomEgypt.corp',
			'ldap_binddn' 	=> "Cairo\\",
			'ldap_search' 	=> "sAMAccountName",
			'ldap_Base_DN' 	=> "DC=Cairo,DC=TelecomEgypt,DC=corp",
			'ldap_password' => '',
			'ldap_username' => 'sAMAccountName'
        ],
	'egypt' =>
		[
			'ldap_host' 	=> 'egypt.te-data.core',
			'ldap_binddn' 	=> "EGYPT\\",
			'ldap_search' 	=> "sAMAccountName",
			'ldap_Base_DN' 	=> "DC=egypt,DC=te-data, DC=core",
			'ldap_password' => 'systemsldapnogoisa',
			'ldap_username' => 'appsauth@te-data.core'
		],
	'active-directory' =>
	    [
			'name'      => 'ad.query',
			'pwd'      => 'AdQu@112233',
			'ldap_host' => "Cairo.TelecomEgypt.corp",
			'ldap_binddn' => "Cairo\\",
			'ldap_rootdn' => "DC=Cairo,DC=TelecomEgypt,DC=corp"
		],
	'mails' =>
	    [
			'cr_manager' => 'sara.mostafa@te.eg',
			'qc_mail' => 'mahmoud.bastawisy@te.eg',
			'ticketing_dev_mail' => 'Ticketing.DEV@te.eg',
			'cr_team' => 'IT.CR@te.eg',
			'qc_team' => 'IT.QC@te.eg',
			'sa_team' => 'IT.SA@te.eg',
			'qa_team' => 'IT.QA@te.eg',
			'as_team' => 'int.app.support@te.eg',
			'bo_team' => 'it.billing.ops@te.eg',
			'pmo_team' => 'Commercial.PMO@te.eg',
			'uat_team' => 'uat-sr@te.eg',
		],
	'group_names' =>
	    [
			'cr_team' => 'CR Team Admin',
			'sa_team' => 'Design team',
			'qc_team' => 'QC team',
			'qa_team' => 'QA',
			'as_team' => 'int.app.support',
			'bo_team' => 'IT Billing Ops',
			'pmo_team' => 'Commercial PMO',
			'uat_team' => 'UAT Promo',
		],	
	'division_managers_mails' =>
		[
			'ahmed.o.hasan@te.eg', 
			//'mahmoud.bastawisy@te.eg', 
			//'sara.mostafa@te.eg', 
			//'yousry.mostafa@te.eg', 
			//'it.qa@te.eg',
		],
	'cr_managers_mails' =>
		[
			//'hanan.megawer@te.eg', 
			'ahmed.o.hasan@te.eg',
			//'mahmoud.bastawisy@te.eg', 
			//'sara.mostafa@te.eg', 
			//'ahmed.elzayat@te.eg',
			//'yousry.mostafa@te.eg', 
			
		],
	'rules' =>
		[
			'notify_division_manager_default' => 'CR Created - Notify Division Manager (Regular)',
			'notify_division_manager_promo' => 'CR Created - Notify Division Manager (Promo)',
		]
];