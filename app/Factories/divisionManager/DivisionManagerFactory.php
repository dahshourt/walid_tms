<?php

namespace App\Factories\divisionManager;

use App\Contracts\FactoryInterface;
use App\Http\Repository\divisionManager\DivisionManagerRepository;

class DivisionManagerFactory implements FactoryInterface
{

	static public function index() {
        return new DivisionManagerRepository();
    }

}