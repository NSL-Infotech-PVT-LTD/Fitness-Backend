<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class categoriesTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        DB::table('categories')->insert([
            [
              'name' => 'admin',
              'user_id' => '1',
            ],
            [
              'name' => 'freelancer',
              'user_id' => '2',
            ],
            [
              'name' => 'client',
              'user_id' => '3',
            ],
             
        ]);
    }

}
