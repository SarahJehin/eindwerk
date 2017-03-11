<?php

use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_row =
            array(
                'name'    => 'Battle Bunny',
                'color'     => '#395696',
                'image'        => 'battle_bunny.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row);

        $category_row2 =
            array(
                'name'    => 'Party Penguin',
                'color'     => '#e51853',
                'image'        => 'party_penguin.png',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );

        DB::table('categories')->insert($category_row2);
    }
}
