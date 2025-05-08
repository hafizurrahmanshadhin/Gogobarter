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
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'date_of_birth'        => '2000-01-01 16:26:32',
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
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'date_of_birth'        => '2000-01-01 16:26:32',
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
                'name'                 => 'trade',
                'email'                => 'trade@trade.com',
                'email_verified_at'    => Carbon::now(),
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'date_of_birth'        => '2000-01-01 16:26:32',
                'otp_verified_at'      => null,
                'role'                 => 'trade',
                'status'               => 'active',
                'remember_token'       => null,
                'created_at'           => now(),
                'updated_at'           => now(),
                'deleted_at'           => null,
            ],
            [
                'id'                   => 4,
                'name'                 => 'Hafizur Rahman Shadhin',
                'email'                => 'shadhin666@gmail.com',
                'email_verified_at'    => Carbon::now(),
                'password'             => Hash::make('12345678'),
                'terms_and_conditions' => 1,
                'avatar'               => null,
                'cover_photo'          => null,
                'date_of_birth'        => '2000-01-01 16:26:32',
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
