<?php

namespace App\Factories\CustomField;

use App\Contracts\FactoryInterface;
use App\Http\Repository\CustomField\CustomFieldGroupTypeRepository;

class CustomFieldGroupTypeFactory implements FactoryInterface
{

	static public function index() {
        return new CustomFieldGroupTypeRepository();
    }

}