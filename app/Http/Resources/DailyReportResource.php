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

        $final_amount = null;


        foreach ($this->paymentLogs as $test) {
            $final_amount = $final_amount + $test->final_amount; 
        }


        return [

            'created_at' => $this->created_at->format('Y-m-d H:i'),
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'balance' => $this->balance,
            'final_amount' => $final_amount,

        ];
    }
}
