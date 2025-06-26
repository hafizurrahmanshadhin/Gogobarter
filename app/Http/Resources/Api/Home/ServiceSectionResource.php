<?php

namespace App\Http\Resources\Api\Home;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceSectionResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $items = is_array($this->items) ? $this->items : [];

        // Map each item to add the base URL to the image
        return array_map(function ($item) {
            $item['image'] = $item['image']
            ? (str_starts_with($item['image'], 'http')
                ? $item['image'] : (file_exists(public_path(ltrim($item['image'], '/')))
                    ? asset(ltrim($item['image'], '/')) : asset('backend/images/users/user-dummy-img.jpg')))
            : asset('backend/images/users/user-dummy-img.jpg');
            return $item;
        }, $items);
    }
}
