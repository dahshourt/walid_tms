<?php

namespace App\Http\Repository\HoldReason;

use App\Contracts\HoldReason\HoldReasonRepositoryInterface;
use App\Models\HoldReason;

class HoldReasonRepository implements HoldReasonRepositoryInterface
{
    public function getAll()
    {
        return HoldReason::all();
    }

    public function create($request)
    {
        return HoldReason::create($request);
    }

    public function delete($id)
    {
        return HoldReason::destroy($id);
    }

    public function update($request, $id)
    {
        return HoldReason::where('id', $id)->update($request);
    }

    public function find($id)
    {
        return HoldReason::find($id);
    }
}
