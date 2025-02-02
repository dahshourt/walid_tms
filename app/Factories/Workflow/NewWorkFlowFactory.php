<?php

namespace App\Factories\Workflow;

use App\Contracts\FactoryInterface;
use App\Http\Repository\NewWorkflow\NewWorkflowRepository;

class NewWorkFlowFactory implements FactoryInterface
{

	static public function index() {
        return new NewWorkflowRepository();
    }

}