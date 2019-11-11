<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PaymentLogs extends JsonResource
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
            'origin' => $this->fare->origin,
            'destination' => $this->fare->destination,
            'fare' => $this->fare->fare,
            'quantity' => $this->quantity,
            'final_amount' => $this->final_amount,
            'created_at' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
