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
                'name'       => 'Battle Bunny',
                'color'      => '#395696',
                'image'      => 'battle_bunny.png',
                'root'       => 'youth',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row);

        $category_row2 =
            array(
                'name'       => 'Party Penguin',
                'color'      => '#e51853',
                'image'      => 'party_penguin.png',
                'root'       => 'youth',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row2);

        //adults
        $category_row3 =
            array(
                'name'       => 'Tornooi',
                'color'      => '#5abfcc',
                'image'      => 'adult_tornooi.png',
                'root'       => 'adult',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row3);

        $category_row4 =
            array(
                'name'       => 'Tennisactiviteit',
                'color'      => '#e8780a',
                'image'      => 'adult_tennisactiviteit.png',
                'root'       => 'adult',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row4);

        /*
        $category_row5 =
            array(
                'name'       => 'Clubdagen',
                'color'      => '#d07821',
                'image'      => 'adult_clubdagen.png',
                'root'       => 'adult',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row5);
        */

        $category_row6 =
            array(
                'name'       => 'Andere',
                'color'      => '#751a8b',
                'image'      => 'adult_andere.png',
                'root'       => 'adult',
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('categories')->insert($category_row6);
    }
}
