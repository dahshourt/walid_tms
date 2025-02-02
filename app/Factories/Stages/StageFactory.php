<?php

namespace App\Factories\stages;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Stages\StageRepository;

class StageFactory implements FactoryInterface
{

	static public function index() {
        return new StageRepository();
    }

}