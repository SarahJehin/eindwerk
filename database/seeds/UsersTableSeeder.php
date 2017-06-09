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
        
        $user_row =
            array(
                'first_name'    => 'Sarah',
                'last_name'     => 'Jehin',
                'vtv_nr'        => '0044596',
                'member_since'  => '2008-01-01',
                'email'         => 'sarah.jehin@belgacom.net',
                'gsm'           => '0495408717',
                'birth_date'    => '1996-04-24',
                'gender'        => 'V',
                'ranking_singles'       => 'C+30/3 (15)',
                'ranking_doubles'       => 'C+30/3 (15)',
                'image'         => 'sarah_jehin.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('sportiva'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row);
        
        $my_id = DB::table('users')->where('last_name', '=', 'Jehin')->select('id')->first()->id;
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
        DB::table('role_user')->insert(['user_id' => $my_id, 'role_id' => 2]);
        DB::table('role_user')->insert(['user_id' => $my_id, 'role_id' => 6]);
        DB::table('role_user')->insert(['user_id' => $my_id, 'role_id' => 10]);
        DB::table('role_user')->insert(['user_id' => $my_id, 'role_id' => 15]);


        //some extra users
        /*
        $user_row2 =
            array(
                'first_name'    => 'Glass',
                'last_name'     => 'Sorenson',
                'vtv_nr'        => '0044597',
                'member_since'  => '2008-01-01',
                'email'         => 'glass.sorenson@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1994-08-22',
                'gender'        => 'V',
                'ranking_singles'       => 'C+30/4 (10)',
                'ranking_doubles'       => 'C+30/4 (10)',
                'image'         => 'glass_sorenson.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('glass'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row2);


        $role_user2 = array(
            'user_id' => DB::table('users')
                ->where('last_name', '=', 'Sorenson')
                ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                ->where('level', '=', 20)
                ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user2);
        */

        $user_row3 =
            array(
                'first_name'    => 'Jorien',
                'last_name'     => 'Geenen',
                'vtv_nr'        => '0718080',
                'member_since'  => '2004-01-01',
                'email'         => 'jorien.geenen@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1996-04-14',
                'gender'        => 'V',
                'ranking_singles'       => 'C+30/2 (20)',
                'ranking_doubles'       => 'C+30/2 (20)',
                'image'         => 'jorien_geenen.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('jorien'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row3);


        $role_user3 = array(
            'user_id' => DB::table('users')
                ->where('last_name', '=', 'Geenen')
                ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                ->where('level', '=', 20)
                ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user3);

        $user_row4 =
            array(
                'first_name'    => 'Sarah',
                'last_name'     => 'Huysmans',
                'vtv_nr'        => '0753883',
                'member_since'  => '2005-01-01',
                'email'         => 'sarah.huysmans@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1990-08-24',
                'gender'        => 'V',
                'ranking_singles'       => 'C+30/4 (10)',
                'ranking_doubles'       => 'C+30/4 (10)',
                'image'         => 'sarah_huysmans.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('sarah_huysmans'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row4);


        $role_user4 = array(
            'user_id' => DB::table('users')
                ->where('last_name', '=', 'Huysmans')
                ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                ->where('level', '=', 20)
                ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user4);

        $user_row5 =
            array(
                'first_name'    => 'Evelynne',
                'last_name'     => 'Nuyens',
                'vtv_nr'        => '0704698',
                'member_since'  => '2003-01-01',
                'email'         => 'evelynne.nuyens@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1992-10-20',
                'gender'        => 'V',
                'ranking_singles'       => 'C+30/3 (15)',
                'ranking_doubles'       => 'C+30/3 (15)',
                'image'         => 'evelynne_nuyens.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('evelynne'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row5);


        $role_user5 = array(
            'user_id' => DB::table('users')
                ->where('last_name', '=', 'Nuyens')
                ->select('id')->first()->id,
            'role_id' => DB::table('roles')
                ->where('level', '=', 20)
                ->select('id')->first()->id,
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        );
        DB::table('role_user')->insert($role_user5);


        //regular users
        /*
        $user_row6 =
            array(
                'first_name'    => 'Shawn',
                'last_name'     => 'Huyon',
                'vtv_nr'        => '0000001',
                'member_since'  => '2008-01-01',
                'email'         => 'shawn.huyon@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1994-11-22',
                'gender'        => 'M',
                'ranking_singles'       => 'C+30/2 (20)',
                'ranking_doubles'       => 'C+30/2 (20)',
                'image'         => 'shawn_huyon.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('shawn'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row6);

        $user_row7 =
            array(
                'first_name'    => 'Finn',
                'last_name'     => 'Harries',
                'vtv_nr'        => '0000002',
                'member_since'  => '2008-01-01',
                'email'         => 'finn.harries@tcs.be',
                'gsm'           => '',
                'birth_date'    => '1993-05-13',
                'gender'        => 'M',
                'ranking_singles'       => 'C+30/3 (15)',
                'ranking_doubles'       => 'C+30/1 (25)',
                'image'         => 'finn_harries.jpg',
                'level_id'      => 6,
                'tmp_password'  => 'sportiva',
                'password'      => Hash::make('finn'),
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),
            );
        DB::table('users')->insert($user_row7);
        */

    }
}
