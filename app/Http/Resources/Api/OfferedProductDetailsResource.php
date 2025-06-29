<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferedProductDetailsResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $exchangeRequest = $this->exchangeRequest ?? null;
        $message         = $exchangeRequest ? $exchangeRequest->message : null;

        // Map images to full URLs
        $images = [];
        if (is_array($this->images)) {
            foreach ($this->images as $img) {
                if (strpos($img, 'http://') === 0 || strpos($img, 'https://') === 0) {
                    $images[] = $img;
                } else {
                    $images[] = asset('storage/' . $img);
                }
            }
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'images'      => $images,
            'description' => $this->description,
            'condition'   => $this->condition,
            'category'    => $this->category->name ?? null,
            'message'     => $message,
            'created_at'  => $this->created_at ? $this->created_at->diffForHumans() : null,
            'owner'       => [
                'id'      => $this->user->id ?? null,
                'name'    => $this->user->name ?? null,
                'email'   => $this->user->email ?? null,
                'phone'   => $this->user->phone_number ?? null,
                'address' => $this->user->address ?? null,
                'avatar'  => $this->user->avatar ?? null,
            ],
        ];
    }
}
