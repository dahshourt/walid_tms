<?php

namespace App\Factories\Permissions;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Permissions\PermissionRepository;

class PermissionFactory implements FactoryInterface
{

	static public function index() {
        return new PermissionRepository();
    }

}