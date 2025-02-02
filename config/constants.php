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
		]

];