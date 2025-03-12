<?php

namespace App\Factories\Defect;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Defect\DefectRepository;

class DefectFactory implements FactoryInterface
{

	static public function index() {
        return new DefectRepository();
    }

}