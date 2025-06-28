<?php

namespace App\Http\Resources\Api\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class FeatureItemResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $image = null;
        if (is_array($this->images) && count($this->images) > 0) {
            $firstImage = $this->images[0];
            if (str_starts_with($firstImage, 'http://') || str_starts_with($firstImage, 'https://')) {
                $image = $firstImage;
            } else {
                $image = asset($firstImage);
            }
        }

        // Determine if the product is in the authenticated user's favorites
        $isFavorite = false;
        $user       = Auth::user();
        if ($user) {
            $isFavorite = $user->favorites()->where('product_id', $this->id)->exists();
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'image'       => $image,
            'condition'   => $this->condition,
            'address'     => $this->user ? $this->user->address : null,
            'created_at'  => $this->created_at ? $this->created_at->diffForHumans() : null,
            'is_favorite' => $isFavorite,
        ];
    }
}
