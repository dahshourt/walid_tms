<?php

namespace App\Factories\Workflow;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Workflow\WorkflowDependRepository;

class WorkflowDependFactory implements FactoryInterface
{

	static public function index() {
        return new WorkflowDependRepository();
    }

}