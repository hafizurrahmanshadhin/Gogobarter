<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder {
    public function run(): void {
        DB::table('product_categories')->insert([
            [
                'id'         => 1,
                'name'       => 'Antiques',
                'status'     => 'active',
                'created_at' => '2025-06-25 05:18:20',
                'updated_at' => '2025-06-25 05:18:20',
                'deleted_at' => null,
            ],
            [
                'id'         => 2,
                'name'       => 'Art',
                'status'     => 'active',
                'created_at' => '2025-06-25 05:18:31',
                'updated_at' => '2025-06-25 05:18:31',
                'deleted_at' => null,
            ],
            [
                'id'         => 3,
                'name'       => 'Baby',
                'status'     => 'active',
                'created_at' => '2025-06-25 05:18:44',
                'updated_at' => '2025-06-25 05:18:44',
                'deleted_at' => null,
            ],
            [
                'id'         => 4,
                'name'       => 'Books',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:34:59',
                'updated_at' => '2025-06-25 06:34:59',
                'deleted_at' => null,
            ],
            [
                'id'         => 5,
                'name'       => 'Cameras',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:35:07',
                'updated_at' => '2025-06-25 06:35:07',
                'deleted_at' => null,
            ],
            [
                'id'         => 6,
                'name'       => 'Cars, Vehicles & Parts',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:35:17',
                'updated_at' => '2025-06-25 06:35:17',
                'deleted_at' => null,
            ],
            [
                'id'         => 7,
                'name'       => 'Cell Phone',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:35:28',
                'updated_at' => '2025-06-25 06:35:28',
                'deleted_at' => null,
            ],
            [
                'id'         => 8,
                'name'       => 'Computers & Networking',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:35:36',
                'updated_at' => '2025-06-25 06:35:36',
                'deleted_at' => null,
            ],
            [
                'id'         => 9,
                'name'       => 'Jewelry & Watches',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:35:44',
                'updated_at' => '2025-06-25 06:35:44',
                'deleted_at' => null,
            ],
            [
                'id'         => 10,
                'name'       => 'Food & Agriculture',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:37:08',
                'updated_at' => '2025-06-25 06:37:08',
                'deleted_at' => null,
            ],
            [
                'id'         => 11,
                'name'       => 'Music & Instruments',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:37:29',
                'updated_at' => '2025-06-25 06:37:29',
                'deleted_at' => null,
            ],
            [
                'id'         => 12,
                'name'       => 'Sporting Goods',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:38:43',
                'updated_at' => '2025-06-25 06:38:43',
                'deleted_at' => null,
            ],
            [
                'id'         => 13,
                'name'       => 'Video Games & Consoles',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:38:58',
                'updated_at' => '2025-06-25 06:38:58',
                'deleted_at' => null,
            ],
            [
                'id'         => 14,
                'name'       => 'Other Stuff',
                'status'     => 'active',
                'created_at' => '2025-06-25 06:39:08',
                'updated_at' => '2025-06-25 06:39:08',
                'deleted_at' => null,
            ],
        ]);
    }
}
