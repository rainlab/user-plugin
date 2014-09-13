<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use RainLab\User\Models\User;

class UsersAddLoginColumn extends Migration
{
    public function up()
    {
        if(!Schema::hasColumn('users', 'login'))
        {
            Schema::table('users', function($table)
            {
                $table->string('login')->unique()->index();
            });
        }
        /*
         * Set login for existing users
         */
        $users = User::all();
        foreach ($users as $user) {
            $user->login = $user->email;
            $user->save();
        }
    }

    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('login');
        });
    }

}
