<?php

namespace App\Factories\ChangeRequest;

use App\Contracts\FactoryInterface;
use App\Http\Repository\ChangeRequest\ChangeRequestRepository;

class ChangeRequestFactory implements FactoryInterface
{

	static public function index() {
        return new ChangeRequestRepository();
    }

}