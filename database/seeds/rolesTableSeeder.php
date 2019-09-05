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
              'label' => 'super-admin',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'coach',
              'label' => 'coach',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'athlete',
              'label' => 'athlete',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
           [
              'name' => 'organizer',
              'label' => 'organizer',
              'created_at' => \Carbon\Carbon::now(),
              'updated_at' => \Carbon\Carbon::now(),
            ],
             
             
        ]);
        
    }

}
