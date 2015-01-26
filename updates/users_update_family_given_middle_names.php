<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use RainLab\User\Models\User;

class UsersUpdateFamilyGivenMiddleNames extends Migration
{

    public function run()
    {
        $usr = User::get();

        foreach ($usr as $user)
        {
            if (!$user->given_name)
            {
                $pcs = explode(' ',$user->name);
                $cnt = count($pcs);
                
                $user->given_name = $pcs[0];
                $user->middle_initial =  $cnt>2?$pcs[1]:'';
                $user->family_name =  $cnt>2?$pcs[2]:$pcs[1];
                
                $user->save();
            }
        }
    }
}
