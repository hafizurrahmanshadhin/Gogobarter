<?php

namespace App\Http\Resources\Api\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureItemResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'image'     => is_array($this->images) && count($this->images) > 0 ? $this->images[0] : null,
            'condition' => $this->condition,
        ];
    }
}
