<?php

namespace App\Http\Resources\Api\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'images'           => $this->images,
            'description'      => $this->description,
            'created_at'       => $this->created_at ? $this->created_at->diffForHumans() : null,
            'owner'            => $this->user ? [
                'id'      => $this->user->id,
                'name'    => $this->user->name,
                'email'   => $this->user->email,
                'phone'   => $this->user->phone_number,
                'address' => $this->user->address,
                'avatar'  => $this->user->avatar,
            ] : null,
        ];
    }
}
