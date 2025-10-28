<?php

namespace App\Exports;

use App\Models\Status;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StatusesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Status::with([
            'stage:id,name',
            'setByGroupStatuses.group:id,title',
            'viewByGroupStatuses.group:id,title'
        ])->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Status Name',
            'Stage',
            'Set By Group',
            'View By Group',
            'Active'
        ];
    }

    /**
     * @param \App\Models\Status $status
     * @return array
     */
    public function map($status): array
    {
        $setByGroups = '';
        if ($status->setByGroupStatuses) {
            $setByGroups = $status->setByGroupStatuses->pluck('group.title')->implode(', ');
        }

        $viewByGroups = '';
        if ($status->viewByGroupStatuses) {
            $viewByGroups = $status->viewByGroupStatuses->pluck('group.title')->implode(', ');
        }

        return [
            $status->id,
            $status->name,
            $status->stage->name,
            $setByGroups,
            $viewByGroups,
            $status->active ? 'Active' : 'Inactive'
        ];
    }
}
