<?php

namespace App\Factories\Units;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Units\UnitRepository;

class UnitFactory implements FactoryInterface
{

	static public function index() {
        return new UnitRepository();
    }

}