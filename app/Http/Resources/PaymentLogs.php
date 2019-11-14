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
        $discounted = ($this->fare->fare * $this->quantity) * 0.20;
        return [
            'id' => $this->id,
            'origin' => $this->fare->origin,
            'destination' => $this->fare->destination,
            'fare' => $this->fare->fare,
            'quantity' => $this->quantity,
            'initial_amount' => $discounted,
            'final_amount' => $this->final_amount,
            'created_at' => $this->created_at->format('Y-m-d H:i')
        ];
    }
}
