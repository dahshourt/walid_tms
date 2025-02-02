<?php

namespace App\Factories\ChangeRequest;

use App\Contracts\FactoryInterface;
use App\Http\Repository\ChangeRequest\ChangeRequestStatusRepository;

class ChangeRequestStatusFactory implements FactoryInterface
{

	static public function index() {
        return new ChangeRequestStatusRepository();
    }

}