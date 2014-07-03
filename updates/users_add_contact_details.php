<?php namespace October\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UsersAddContactDetails extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('phone', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('street_addr')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('zip', 20)->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function($table)
        {
            $table->dropColumn('phone');
            $table->dropColumn('company');
            $table->dropColumn('street_addr');
            $table->dropColumn('city');
            $table->dropColumn('zip');
        });
    }

}
