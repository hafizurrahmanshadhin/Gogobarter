<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory {
    protected $model = Favorite::class;

    public function definition(): array {
        return [
            'user_id'    => User::inRandomOrder()->first()?->id ?? 1,
            'product_id' => Product::inRandomOrder()->first()?->id ?? 1,
        ];
    }
}
