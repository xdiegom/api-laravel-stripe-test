<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'alias' => $this->alias,
            'brand' => $this->brand,
            'default' => boolval($this->default),
            'card_number' => str_pad($this->last4, 16, '*', STR_PAD_LEFT),
            'expiration_year' => $this->expiration_year,
            'expiration_month' => $this->expiration_month
        ];
    }
}
