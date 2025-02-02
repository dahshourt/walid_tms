<?php

namespace App\Factories\Users;

use App\Contracts\FactoryInterface;
use App\Http\Repository\Users\UserRepository;

class UserFactory implements FactoryInterface
{

	static public function index() {
        return new UserRepository();
    }

}