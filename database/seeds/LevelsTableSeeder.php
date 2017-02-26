<?php

use Illuminate\Database\Seeder;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels')->delete();
        
        $levels = array('Wit',
                        'Blauw',
                        'Rood',
                        'Oranje',
                        'Groen',
                        'Maxi'
                       );

        $nr = 1;
        foreach ($levels as $level) {
            $level_entry = array(
                    'number'        => $nr;
                    'name'          => $level,
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                );
            DB::table('level')->insert($level_entry);
            $nr++;
        }
        
    }
}
