<?php

namespace App\Factories\RejectionReasons;

use App\Contracts\FactoryInterface;
use App\Http\Repository\RejectionReasons\RejectionReasonsRepository;

class RejectionReasonsFactory implements FactoryInterface
{

	static public function index() {
        return new RejectionReasonsRepository();
    }

}