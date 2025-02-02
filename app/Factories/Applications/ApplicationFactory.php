<?php

namespace App\Factories\Applications;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Applications\ApplicationRepository;

class ApplicationFactory implements FactoryInterface
{

	static public function index() {
        return new ApplicationRepository();
    }

}