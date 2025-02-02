<?php

namespace App\Factories\Categories;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Categories\CategoreyRepository;

class CategoryFactory implements FactoryInterface
{

	static public function index() {
        return new CategoreyRepository();
    }

}