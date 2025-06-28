<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {
    protected $model = Product::class;

    public function definition(): array {
        $images     = [];
        $imageCount = $this->faker->numberBetween(2, 5);
        for ($i = 0; $i < $imageCount; $i++) {
            $seed     = $this->faker->uuid . '_' . $i;
            $images[] = "https://picsum.photos/seed/{$seed}/400/300";
        }

        return [
            'user_id'             => User::inRandomOrder()->first()?->id ?? 1,
            'product_category_id' => ProductCategory::inRandomOrder()->first()?->id ?? 1,
            'name'                => $this->faker->words(2, true),
            'images'              => $images,
            'description'         => $this->faker->sentence(),
            'condition'           => $this->faker->randomElement(['New', 'Old']),
            'status'              => 'active',
        ];
    }
}
