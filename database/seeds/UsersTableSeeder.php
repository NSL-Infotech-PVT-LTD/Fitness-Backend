<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'),
                'experience' => 'experience',
                'hourly_rate' => 50,
                'latitude' => 30.733315,
                'longitude' => 76.779419,
                'image' => '2.png',
                'biography' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
            ],
            
            [
                'name' => 'freelancer',
                'email' => 'freelancer@gmail.com',
                'password' => Hash::make('12345678'),
                'experience' => 'experience',
                'hourly_rate' => 50,
                'latitude' => 30.733315,
                'longitude' => 76.779419,
                'image' => '3.png',
                'biography' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
            ],
            
           [
                'name' => 'Client',
                'email' => 'client@gmail.com',
                'password' => Hash::make('12345678'),
                'experience' => 'experience',
                'hourly_rate' => 50,
                'latitude' => 30.733315,
                'longitude' => 76.779419,
                'image' => '4.png',
                'biography' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
            ],
        ]);
    }

}
