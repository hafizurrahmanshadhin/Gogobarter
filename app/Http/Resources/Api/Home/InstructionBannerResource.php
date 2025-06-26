<?php

namespace App\Http\Resources\Api\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructionBannerResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $image = $this->image;

        if (is_string($image) && str_contains($image, '/storage/')) {
            $image = str_replace('/storage/', '/', $image);
            $image = asset(ltrim($image, '/'));
        }

        return [
            'title' => $this->title,
            'image' => $image,
        ];
    }
}
