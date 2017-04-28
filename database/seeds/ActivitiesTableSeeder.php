<?php

use Illuminate\Database\Seeder;

class ActivitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $activity_row01 =
            array(
                'title'    			=> 'Lasershooting',
                'description'   	=> 'Op 5 mei gaan we lasershooten met onze iets oudere jeugd.  Heb jij ook zin in een avontuurlijke avond met je vrienden? Schrijf je dan zeker in!',
                'poster'        	=> '1489930056lasershooting.jpg',
                'extra_url'     	=> null,
                'start'         	=> '2017-05-05 18:00:00',
                'deadline'      	=> '2017-04-30 00:00:00',
                'end'           	=> '2017-05-05 20:00:00',
                'location'      	=> 'Sportiva (Industriepark 5, Hulshout)',
                'latitude'      	=> '51.083253',
                'longitude'     	=> '4.805906',
                'min_participants'  => 8,
                'max_participants'  => 20,
                'helpers'         	=> 2,
                'price'         	=> 8,
                'is_visible'        => 1,
                'category_id'       => 2,
                'made_by_id'        => 1,
                'owner_id'         	=> 1,
                'created_at' 		=> \Carbon\Carbon::now(),
                'updated_at' 		=> \Carbon\Carbon::now(),
            );
        DB::table('activities')->insert($activity_row01);

        $activity_row02 =
            array(
                'title'    			=> 'Wintercompetitie 1 april',
                'description'   	=> 'Op 1 april is het de laatste wintercompetitie. Zorg dat je er zeker bij bent!',
                'poster'        	=> '1490733466wintercompetitie_1_april.png',
                'extra_url'     	=> null,
                'start'         	=> '2017-04-01 19:00:00',
                'deadline'      	=> null,
                'end'           	=> '2017-04-01 21:00:00',
                'location'      	=> 'Sportiva (Industriepark 5, Hulshout)',
                'latitude'      	=> '51.083253',
                'longitude'     	=> '4.805906',
                'min_participants'  => 8,
                'max_participants'  => 20,
                'helpers'         	=> 2,
                'price'         	=> 0,
                'is_visible'        => 1,
                'category_id'       => 1,
                'made_by_id'        => 1,
                'owner_id'         	=> 1,
                'created_at' 		=> \Carbon\Carbon::now(),
                'updated_at' 		=> \Carbon\Carbon::now(),
            );
        DB::table('activities')->insert($activity_row02);

        $activity_row03 =
            array(
                'title'    			=> 'Discobowling !',
                'description'   	=> 'We gaan weer bowlen !<br><p>Op 26 mei gaan we er een feestje van maken in de discobowling in Lier.</p><p>Vergeet geen <b>€5</b> over te schrijven naar <b>BE66 7333 2013 0443</b>.</p><p>Tot dan !<br></p><p><br></p>',
                'poster'        	=> '1493312763discobowling.jpg',
                'extra_url'     	=> null,
                'start'         	=> '2017-05-26 19:00:00',
                'deadline'      	=> '2017-05-21 00:00:00',
                'end'           	=> '2017-05-26 21:30:00',
                'location'      	=> 'Superbowl, Mechelsesteenweg, Lier, België',
                'latitude'      	=> '51.126470',
                'longitude'     	=> '4.555280',
                'min_participants'  => 8,
                'max_participants'  => 24,
                'helpers'         	=> 2,
                'price'         	=> 5,
                'is_visible'        => 1,
                'category_id'       => 2,
                'made_by_id'        => 1,
                'owner_id'         	=> 2,
                'created_at' 		=> \Carbon\Carbon::now(),
                'updated_at' 		=> \Carbon\Carbon::now(),
            );
        DB::table('activities')->insert($activity_row03);
    }
}
