<?php

namespace App\Factories\MailTemplates;

use App\Contracts\FactoryInterface;
use App\Http\Repository\MailTemplates\MailTemplatesRepository;

class MailTemplatesFactory implements FactoryInterface
{

	static public function index() {
        return new MailTemplatesRepository();
    }

}