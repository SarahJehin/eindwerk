<?php

use Illuminate\Database\Seeder;

class TagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->delete();
        
        $tags = array( 'niveau'			=> array(	'wit', 
        											'blauw', 
        											'rood', 
        											'oranje', 
        											'groen', 
        											'maxi'
        										),
                       'slag'			=> array(	'forehand', 
                       								'backhand', 
                       								'volley', 
                       								'inloopvolley', 
                       								'drivevolley', 
                       								'smash', 
                       								'lob', 
                       								'dropshot', 
                       								'slice', 
                       								'topspin', 
                       								'opslag/service'
                       							),
                       'soort oefening' => array(	'conditie', 
                       								'coördinatie', 
                       								'netspel', 
                       								'diepe ballen', 
                       								'cross', 
                       								'inside in', 
                       								'inside out', 
                       								'wedstrijdsituatie', 
                       								'enkelspel', 
                       								'dubbelspel'
                       							)
                       );

        
        foreach ($tags as $type => $values) {
        	foreach ($values as $name) {
        		$tag = array(
                    'type'	=> $type,
                    'name'	=> $name,
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                );
            	DB::table('tags')->insert($tag);
        	}
        }
    }
}
