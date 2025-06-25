<?php

namespace App\Http\Resources\Api\Product;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'product_category_id' => $this->product_category_id,
            'name'                => $this->name,
            'images'              => $this->images,
            'description'         => $this->description,
            'condition'           => $this->condition,
        ];
    }
}
