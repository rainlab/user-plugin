<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UsersAddFamilyGivenMiddleNames extends Migration
{

    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('given_name')->nullable();
            $table->char('middle_initial', 1)->nullable();
            $table->string('family_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('given_name');
            $table->dropColumn('middle_initial');
            $table->dropColumn('family_name');
        });
    }

}
