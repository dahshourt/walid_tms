<?php

namespace App\Http\Controllers\Search;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TableExport implements FromCollection, WithHeadings, WithMapping
{
    protected $items;

    public function __construct($items)
    {
        $this->items = $items;
    }

    public function collection()
    {
        // Return the collection of items to be exported
        return collect($this->items);
    }

    public function headings(): array
    {
        return [
            'CR ID',
            'Title',
            'Category',
            'Release',
            'Current Status',
            'Requester',
            'Requester Email',
            'Design Duration',
            'Dev Duration',
            'Test Duration',
            'Creation Date',
            'Requesting Department',
            'Targeted System',
            'Last Action Date',

        ];
    }

    public function map($item): array
    {
        return [
            $item['cr_no'],
            $item['title'],
            $item['category']['name'] ?? '',
            $item['application']['name'] ?? '',
            $item->getCurrentStatus()->status->status_name ?? '',
            $item['requester_name'] ?? '',
            $item['requester_email'] ?? '',
            $item['design_duration'] ?? '',
            $item['develop_duration'] ?? '',
            $item['test_duration'] ?? '',
            $item['created_at'] ?? '',
            $item['department'] ?? '',
            $item['application']['name'] ?? '',
            $item['updated_at'] ?? '',
        ];
    }
}
