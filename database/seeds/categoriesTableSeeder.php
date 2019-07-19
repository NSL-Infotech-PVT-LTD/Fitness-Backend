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
            ],
            [
                'name' => 'freelancer',
            ],
            [
                'name' => 'client',
            ],
        ]);
    }

}
