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
              'name' => 'admin',
              'label' => 'Admin',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'freelancer',
              'label' => 'Freelancer',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'client',
              'label' => 'Client',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
             
             
        ]);
        
    }

}
