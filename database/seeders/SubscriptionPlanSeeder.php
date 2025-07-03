<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder {
    public function run(): void {
        $subscriptionPlans = [
            [
                'name'             => 'Free',
                'billing_interval' => 'year',
                'price'            => 0.00,
                'currency'         => 'USD',
                'description'      => 'Affordable solo marketer solution. Monthly or annual billing. Full feature set.',
                'features'         => [
                    '1 Post',
                    'No Featured Posts',
                    'No Priority Listing',
                ],
                'is_recommended'   => false,
                'status'           => 'active',
            ],
            [
                'name'             => 'Professional',
                'billing_interval' => 'year',
                'price'            => 1.99,
                'currency'         => 'USD',
                'description'      => 'Affordable solo marketer solution. Monthly or annual billing. Full feature set.',
                'features'         => [
                    'Up to 5 Posts',
                    '1 Featured Post',
                    'No Priority Listing',
                ],
                'is_recommended'   => false,
                'status'           => 'inactive',
            ],
            [
                'name'             => 'Organization',
                'billing_interval' => 'year',
                'price'            => 4.99,
                'currency'         => 'USD',
                'description'      => 'Affordable solo marketer solution. Monthly or annual billing. Full feature set.',
                'features'         => [
                    'Unlimited Posts',
                    'Best for professionals',
                    'Priority Listing ( Search Priority )',
                ],
                'is_recommended'   => true,
                'status'           => 'active',
            ],
        ];

        foreach ($subscriptionPlans as $plan) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $plan['name']],
                $plan
            );
        }
    }
}
