<?php

namespace App\Http\Resources\Api\Product;

use App\Models\ExchangeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        $user = auth('api')->user() ?? auth()->user(); // support both guards

        // Default message is null
        $message = null;

        if ($user) {
            $exchangeRequest = ExchangeRequest::where('requester_id', $user->id)
                ->where('requested_product_id', $this->id)
                ->latest()
                ->first();

            if ($exchangeRequest) {
                $message = $exchangeRequest->message;
            }
        }

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'images'      => $this->images,
            'description' => $this->description,
            'created_at'  => $this->created_at ? $this->created_at->diffForHumans() : null,
            'message'     => $message,
            'owner'       => $this->user ? [
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
