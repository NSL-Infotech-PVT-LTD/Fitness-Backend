<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class rolesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
       DB::table('roles')->insert([
            [
              'name' => 'super admin',
              'label' => 'Admin',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'admin',
              'label' => 'salon-admin',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'customer',
              'label' => 'user',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
             
             
        ]);
        
    }

}
