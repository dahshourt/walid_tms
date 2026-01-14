<?php

use App\Models\CustomField;
use App\Models\ChangeRequestCustomField;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$field = CustomField::where('name', 'need_design')->first();

if ($field) {
    echo "Found CustomField:\n";
    print_r($field->toArray());
} else {
    echo "CustomField 'need_design' not found.\n";
}

$crField = ChangeRequestCustomField::where('custom_field_name', 'need_design')->first();
if ($crField) {
    echo "\nFound ChangeRequestCustomField usage:\n";
    print_r($crField->toArray());
} else {
    echo "\nChangeRequestCustomField 'need_design' not found.\n";
}
