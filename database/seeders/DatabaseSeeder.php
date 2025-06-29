<?php

namespace Database\Seeders;

use Database\Seeders\CMSSeeder;
use Database\Seeders\ContentSeeder;
use Database\Seeders\DynamicPageSeeder;
use Database\Seeders\ExchangeRequestSeeder;
use Database\Seeders\FavoriteSeeder;
use Database\Seeders\ProductCategorySeeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\SocialMediaSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Database\Seeders\SystemSettingSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            UserSeeder::class,
            SystemSettingSeeder::class,
            DynamicPageSeeder::class,
            SocialMediaSeeder::class,
            ContentSeeder::class,
            CMSSeeder::class,
            SubscriptionPlanSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            FavoriteSeeder::class,
            ExchangeRequestSeeder::class,
        ]);
    }
}
