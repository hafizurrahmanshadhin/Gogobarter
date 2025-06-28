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
        $images = [];
        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
                    $images[] = $img;
                } else {
                    $images[] = asset($img);
                }
            }
        }

        return [
            'id'                  => $this->id,
            'user_id'             => $this->user_id,
            'product_category_id' => $this->product_category_id,
            'name'                => $this->name,
            'description'         => $this->description,
            'condition'           => $this->condition,
            'images'              => $images,
        ];
    }
}
