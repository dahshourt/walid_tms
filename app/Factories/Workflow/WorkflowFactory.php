<?php

namespace App\Factories\Workflow;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Workflow\WorkflowRepository;

class WorkflowFactory implements FactoryInterface
{

	static public function index() {
        return new WorkflowRepository();
    }

}