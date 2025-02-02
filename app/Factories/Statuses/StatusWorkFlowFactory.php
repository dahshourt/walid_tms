<?php

namespace App\Factories\statuses;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Statuses\StatusWorkFlowRepository;

class StatusWorkFlowFactory implements FactoryInterface
{

	static public function index() {
        return new StatusWorkFlowRepository();
    }

}