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
//        DB::table('users')->insert([
//            [
//                'firstname' => 'admin',
//                'lastname' => 'admin',
//                'email' => 'admin@wellgellondon.com',
//                'password' => Hash::make('12345678'),
//                'phone' => '98166422'
//            ]
//        ]);
        $data = [
            'name' => 'admin',
            'email' => 'admin@utrain.com',
            'password' => Hash::make('12345678'),
            'phone' => '98166422',
            
        ];
        $user = \App\User::create($data);
        $user->assignRole('super admin');
    }

}
