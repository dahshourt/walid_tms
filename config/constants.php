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
			'qc_mail' => 'QCautomation5@te.eg',
			'ticketing_dev_mail' => 'Ticketing.DEV@te.eg'
		],
	'division_managers_mails' =>
		[
			
			//'anan.latif@te.eg', 
			//'reem.mahrous@te.eg', 
			//'adel.atef@te.eg', 
			//'yousry.mostafa@te.eg', 
			//'it.qa@te.eg'
		]
];