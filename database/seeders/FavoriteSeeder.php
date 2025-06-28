<?php

namespace Database\Seeders;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder {
    public function run(): void {
        $users      = User::all();
        $productIds = Product::pluck('id')->toArray();

        foreach ($users as $user) {
            $favoritesCount     = rand(20, 30);
            $favoriteProductIds = collect($productIds)->shuffle()->take($favoritesCount);

            foreach ($favoriteProductIds as $productId) {
                Favorite::firstOrCreate([
                    'user_id'    => $user->id,
                    'product_id' => $productId,
                ]);
            }
        }
    }
}
