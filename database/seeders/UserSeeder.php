<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        DB::table('users')->insert([
            [
                'id'                   => 1,
                'name'                 => 'admin',
                'email'                => 'admin@admin.com',
                'email_verified_at'    => Carbon::now(),
                'phone_number'         => '1234567890',
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'address'              => 'New York, USA',
                'otp_verified_at'      => null,
                'role'                 => 'admin',
                'status'               => 'active',
                'remember_token'       => null,
                'created_at'           => now(),
                'updated_at'           => now(),
                'deleted_at'           => null,
            ],
            [
                'id'                   => 2,
                'name'                 => 'user',
                'email'                => 'user@user.com',
                'email_verified_at'    => Carbon::now(),
                'phone_number'         => '0123456789',
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'address'              => 'Los Angeles, USA',
                'otp_verified_at'      => null,
                'role'                 => 'user',
                'status'               => 'active',
                'remember_token'       => null,
                'created_at'           => now(),
                'updated_at'           => now(),
                'deleted_at'           => null,
            ],
            [
                'id'                   => 3,
                'name'                 => 'Hafizur Rahman Shadhin',
                'email'                => 'shadhin666@gmail.com',
                'email_verified_at'    => Carbon::now(),
                'phone_number'         => '9876543210',
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'address'              => 'Chicago, USA',
                'otp_verified_at'      => null,
                'role'                 => 'admin',
                'status'               => 'active',
                'remember_token'       => null,
                'created_at'           => now(),
                'updated_at'           => now(),
                'deleted_at'           => null,
            ],
        ]);
    }
}
