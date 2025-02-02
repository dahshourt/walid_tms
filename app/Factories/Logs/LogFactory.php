<?php

namespace App\Factories\Logs;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Logs\LogRepository;

class LogFactory implements FactoryInterface
{

	static public function index() {
        return new LogRepository();
    }

}
