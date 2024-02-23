<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        $updater = App::make('db.updater');
        $updater->setUp(__DIR__.'/000002_create_password_resets.php');

        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->mediumText('notes')->nullable();
                $table->integer('primary_group_id')->nullable()->unsigned();
                $table->string('remember_token')->nullable();
                $table->boolean('is_banned')->default(false);
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
            });

            Db::update("update users set first_name=name, last_name=surname");
        }
    }

    public function down()
    {
    }
};
