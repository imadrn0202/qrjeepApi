<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Driver extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'plate_number' => $this->plate_number,
            'balance' => $this->balance,
            'operatorEarnings' => $this->balance * 0.65,
            'balanceWithCut' => $this->balance * 0.35
        ];
    }
}
