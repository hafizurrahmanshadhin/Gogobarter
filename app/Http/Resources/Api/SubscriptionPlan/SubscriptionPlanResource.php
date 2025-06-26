<?php

namespace App\Http\Resources\Api\SubscriptionPlan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'billing_interval' => $this->billing_interval,
            'price'            => $this->price,
            'currency'         => $this->currency,
            'description'      => $this->description,
            'features'         => $this->features,
            'is_popular'       => $this->is_recommended,
        ];
    }
}
