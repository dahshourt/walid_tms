<?php

namespace App\Factories\CabUser;

use App\Contracts\FactoryInterface;
use App\Http\Repository\CabUser\CabUserRepository;

class CabUserFactory implements FactoryInterface
{

	static public function index() {
        return new CabUserRepository();
    }

}