<?php

namespace App\Factories\Parents;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Parents\ParentRepository;

class ParentFactory implements FactoryInterface
{

	static public function index() {
        return new ParentRepository();
    }

}