<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        $updater = App::make('db.updater');
        $updater->setUp(__DIR__.'/000002_create_password_resets.php');
        $updater->setUp(__DIR__.'/000003_create_user_roles.php');

        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->mediumText('notes')->nullable();
                $table->integer('role_id')->nullable()->unsigned();
                $table->string('remember_token')->nullable();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
            });
        }
    }

    public function down()
    {
    }
};
