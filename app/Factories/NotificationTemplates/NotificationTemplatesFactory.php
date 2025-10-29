<?php

namespace App\Factories\NotificationTemplates;

use App\Contracts\FactoryInterface;
use App\Http\Repository\NotificationTemplates\NotificationTemplatesRepository;

class NotificationTemplatesFactory implements FactoryInterface
{

	static public function index() {
        return new NotificationTemplatesRepository();
    }

}