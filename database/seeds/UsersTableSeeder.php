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
                'firstname' => 'admin',
                'lastname' => 'admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('12345678'),
                'phone' => '9816642246'
            ],
             [
                'firstname' => 'freelancer',
                'lastname' => 'freelancerlastname',
                'email' => 'freelancer@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '98166422',
                'experience' => 'experience',
                'hourly_rate' => 50,
                'latitude' => 30.733315,
                'longitude' => 76.779419,
                'category_id' => 4,
                'profile_pic' => 'public/adminMedia/images/2.png',
                'bio' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
            ],
            
            [
                'firstname' => 'client',
                'lastname' => 'clientlastanmme',
                'email' => 'client@gmail.com',
                'password' => Hash::make('12345678'),
                'phone' => '98166422',
                'experience' => 'experience',
                'hourly_rate' => 50,
                'latitude' => 30.733315,
                'longitude' => 76.779419,
                'category_id' => 4,
                'profile_pic' => 'public/adminMedia/images/3.png',
                'biography' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
            ],
              
        ]);
    }

}
