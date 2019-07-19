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
                'phone' => '98166422'
            ]
        ]);
    }

}
