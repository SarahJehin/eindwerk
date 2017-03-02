<?php

use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();
        
        $roles = array(10 => 'Bestuur',
                       11 => 'Voorzitter',
                       12 => 'Ondervoorzitter',
                       13 => 'Penningmeester',
                       14 => 'Verantwoordelijke lessen',
                       20 => 'Jeugdbestuur',
                       21 => 'Voorzitter',
                       22 => 'Ondervoorzitter',
                       30 => 'Trainer',
                       31 => 'Hoofdtrainer',
                       32 => 'Trainer A',
                       33 => 'Trainer B',
                       34 => 'Instructeur B',
                       35 => 'Initiator',
                       36 => 'Aspirant-Initiator',
                       40 => 'Clubmedewerker'
                       );

        
        foreach ($roles as $level => $role) {
            $role_entry = array(
                    'name'          => $role,
                    'level'         => $level,
                    'created_at'    => \Carbon\Carbon::now(),
                    'updated_at'    => \Carbon\Carbon::now(),
                );
            DB::table('roles')->insert($role_entry);
        }
    }
}
