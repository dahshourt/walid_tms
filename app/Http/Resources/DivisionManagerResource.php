<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DivisionManagerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                        => $this->id,
<<<<<<< HEAD
<<<<<<< HEAD
            'name'     => $this-> name,
=======
            'name'                      => $this-> name,
>>>>>>> 3025385876fa7855b26320f7130884818602743c
=======

            'name'                      => $this-> name,

>>>>>>> 44c40c2c48d4375e8356d8c56d0d730ad31d0dbd
            'division_manager_email'    => $this-> division_manager_email,
            'active'                    => $this->active,
            'created_at'                => $this->created_at,
            'updated_at'                => $this->updated_at,

        ];
    }
}
