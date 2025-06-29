<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeRequestResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            "id"               => $this->id,
            'requester_id'     => $this->requester->id,
            'requester_name'   => $this->requester->name,
            'requester_avatar' => $this->requester->avatar,
            'requested_at'     => $this->created_at->diffForHumans(),
            'item_wanted'      => $this->requestedProduct->name,
            'item_offered'     => $this->offeredProduct->name,
            'message'          => $this->message,
        ];
    }
}
