<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_groups', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('code')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users_groups', function(Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('user_group_id')->unsigned();
            $table->primary(['user_id', 'user_group_id'], 'rainlab_user_group');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_groups');
        Schema::dropIfExists('users_groups');
    }
};
