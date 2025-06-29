<?php

namespace Database\Factories;

use App\Models\ExchangeRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangeRequestFactory extends Factory {
    protected $model = ExchangeRequest::class;

    public function definition(): array {
        // Get random users and products
        $requester        = User::inRandomOrder()->first();
        $requestedProduct = Product::inRandomOrder()->first();
        $offeredProduct   = Product::inRandomOrder()->first();

        // Ensure products and users are not null and not the same
        if (!$requester || !$requestedProduct || !$offeredProduct || $requestedProduct->user_id == $requester->id || $offeredProduct->user_id != $requester->id) {
            // fallback to IDs 1 if not found
            $requesterId        = 1;
            $requestedProductId = 1;
            $offeredProductId   = 2;
        } else {
            $requesterId        = $requester->id;
            $requestedProductId = $requestedProduct->id;
            $offeredProductId   = $offeredProduct->id;
        }

        return [
            'requester_id'         => $requesterId,
            'requested_product_id' => $requestedProductId,
            'offered_product_id'   => $offeredProductId,
            'message'              => $this->faker->sentence(),
            'status'               => 'pending',
            'created_at'           => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at'           => now(),
        ];
    }
}
