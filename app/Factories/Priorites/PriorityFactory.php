<?php

namespace App\Factories\Priorites;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Priorities\PriorityRepository;

class PriorityFactory implements FactoryInterface
{

	static public function index() {
        return new PriorityRepository();
    }

}