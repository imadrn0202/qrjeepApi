<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DailyReportResource extends JsonResource
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
            'user_type' => $this->user_type,
            'quantity' => $this->quantity,
            'final_amount' => $this->final_amount,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'first_name' => $this->driver->first_name,
            'last_name' => $this->driver->last_name,
            'earnings' => $this->earnings,
            'driver_user_id' => $this->driver_user_id
        ];
    }
}
