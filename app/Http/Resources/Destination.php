<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Destination extends JsonResource
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
            'id' => $this->id,
            'destination' => $this->destination,
            'fare' => $this->fare
        ];
    }
}
