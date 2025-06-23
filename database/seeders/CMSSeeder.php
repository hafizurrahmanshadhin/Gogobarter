<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CMSSeeder extends Seeder {
    public function run(): void {
        DB::table('c_m_s')->insert([
            [
                'id'          => 1,
                'section'     => 'hero',
                'title'       => "Trade What You Don't Need, Get What You Want",
                'sub_title'   => null,
                'description' => null,
                'content'     => '<p>Swap what you don’t need for what you want—cash free trading!</p>',
                'image'       => null,
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 11:50:15',
                'updated_at'  => '2025-06-23 11:50:15',
                'deleted_at'  => null,
            ],
            [
                'id'          => 2,
                'section'     => 'service',
                'title'       => null,
                'sub_title'   => null,
                'description' => null,
                'content'     => null,
                'image'       => null,
                'items'       => '[
                    {
                        "image": "seeder/1.png",
                        "title": "Fair Trade Evaluation",
                        "description": "<p>Get accurate item valuations and fair trade suggestions</p>"
                    },
                    {
                        "image": "seeder/2.png",
                        "title": "Secure Trading",
                        "description": "<p>Trade with confidence using our secure platform</p>"
                    },
                    {
                        "image": "seeder/3.png",
                        "title": "Easy Communication",
                        "description": "<p>Chat directly with potential trading partners</p>"
                    }
                ]',
                'status'      => 'active',
                'created_at'  => '2025-06-23 11:57:37',
                'updated_at'  => '2025-06-23 11:57:37',
                'deleted_at'  => null,
            ],
            [
                'id'          => 3,
                'section'     => 'instruction_banner',
                'title'       => 'Building success, one step at a time',
                'sub_title'   => null,
                'description' => null,
                'content'     => null,
                'image'       => 'seeder/how-it-work.png',
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 12:00:31',
                'updated_at'  => '2025-06-23 12:00:31',
                'deleted_at'  => null,
            ],
            [
                'id'          => 4,
                'section'     => 'instruction',
                'title'       => 'Post Your Item',
                'sub_title'   => null,
                'description' => '<p>Our team conducts comprehensive research to outline a customized strategy.</p>',
                'content'     => null,
                'image'       => null,
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 12:06:41',
                'updated_at'  => '2025-06-23 12:06:41',
                'deleted_at'  => null,
            ],
            [
                'id'          => 5,
                'section'     => 'instruction',
                'title'       => 'Find a Trade',
                'sub_title'   => null,
                'description' => '<p>We create user-centric, scalable solutions using cutting-edge technology.</p>',
                'content'     => null,
                'image'       => null,
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 12:07:06',
                'updated_at'  => '2025-06-23 12:07:06',
                'deleted_at'  => null,
            ],
            [
                'id'          => 6,
                'section'     => 'instruction',
                'title'       => 'Make the Exchange',
                'sub_title'   => null,
                'description' => '<p>We gather feedback and make continuous improvements to ensure sustained success.</p>',
                'content'     => null,
                'image'       => null,
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 12:07:53',
                'updated_at'  => '2025-06-23 12:07:53',
                'deleted_at'  => null,
            ],
            [
                'id'          => 7,
                'section'     => 'trading',
                'title'       => 'Start Trading Now',
                'sub_title'   => null,
                'description' => '<p>Discover a new way to trade! List your items, browse available trades, and connect with others—all without spending a dime. Start trading now!</p>',
                'content'     => null,
                'image'       => null,
                'items'       => null,
                'status'      => 'active',
                'created_at'  => '2025-06-23 12:08:30',
                'updated_at'  => '2025-06-23 12:08:30',
                'deleted_at'  => null,
            ],
        ]);
    }
}
