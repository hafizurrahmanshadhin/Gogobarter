<?php

namespace Database\Seeders;

use App\Models\ExchangeRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExchangeRequestSeeder extends Seeder {
    public function run(): void {
        $statuses = ['pending', 'accepted', 'rejected'];

        foreach (User::all() as $user) {
            foreach ($statuses as $status) {
                $count = rand(10, 15);
                for ($i = 0; $i < $count; $i++) {
                    // Find a product not owned by this user to request
                    $requestedProduct = Product::where('user_id', '!=', $user->id)->inRandomOrder()->first();
                    // Find a product owned by this user to offer
                    $offeredProduct = Product::where('user_id', $user->id)->inRandomOrder()->first();

                    // Skip if not enough products
                    if (!$requestedProduct || !$offeredProduct) {
                        continue;
                    }

                    ExchangeRequest::factory()->create([
                        'requester_id'         => $user->id,
                        'requested_product_id' => $requestedProduct->id,
                        'offered_product_id'   => $offeredProduct->id,
                        'status'               => $status,
                    ]);
                }
            }
        }
    }
}
