<?php

namespace App\Factories\Permissions;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Permissions\ModuleRolesRepository;

class ModuleRoulesFactory implements FactoryInterface
{

	static public function index() {
        return new ModuleRolesRepository();
    }

}