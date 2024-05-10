<?php namespace RainLab\User\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateUserLogsTable Migration
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('rainlab_user_user_logs', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable()->index();
            $table->string('type')->nullable();
            $table->mediumText('data')->nullable();
            $table->mediumText('comment')->nullable();
            $table->boolean('is_comment')->default(false)->index();
            $table->boolean('is_system')->default(false);
            $table->bigInteger('updated_user_id')->unsigned()->nullable();
            $table->bigInteger('created_user_id')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('rainlab_user_user_logs');
    }
};
