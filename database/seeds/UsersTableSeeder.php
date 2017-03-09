<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $user_row =
            array(
                'first_name'    => 'Sarah',
                'last_name'     => 'Jehin',
                'vtv_nr'        => '0044596',
                'email'         => 'sarah.jehin@belgacom.net',
                'gsm'           => '0495408717',
                'birth_date'    => '1996-04-24',
                'gender'        => 'V',
                'ranking'       => 'C+30/3',
                'image'         => 'sarah_jehin.jpg',
                'level_id'      => 6,
                'password'      => Hash::make('sportiva'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row);
        

        $role_user = array(
            'user_id' => DB::table('users')
                    ->where('last_name', '=', 'Jehin')
                    ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                    ->where('level', '=', 20)
                    ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user);
*/

        //some extra users
        $user_row2 =
            array(
                'first_name'    => 'Glass',
                'last_name'     => 'Sorenson',
                'vtv_nr'        => '0044597',
                'email'         => 'glass.sorenson@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1994-08-22',
                'gender'        => 'V',
                'ranking'       => 'C+30/4',
                'image'         => 'sarah_jehin.jpg',
                'level_id'      => 6,
                'password'      => Hash::make('glass'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row2);


        $role_user2 = array(
            'user_id' => DB::table('users')
                ->where('last_name', '=', 'Jehin')
                ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                ->where('level', '=', 20)
                ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user2);
    }
}
