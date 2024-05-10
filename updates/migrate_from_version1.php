<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        $updater = App::make('db.updater');
        if (!Schema::hasTable('user_password_resets')) {
            $updater->setUp(__DIR__.'/000002_create_password_resets.php');
            $updater->setUp(__DIR__.'/000004_create_user_preferences.php');
            $updater->setUp(__DIR__.'/000005_create_user_logs.php');
        }

        if (!Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function(Blueprint $table) {
                $table->boolean('is_mail_blocked')->default(false);
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->mediumText('notes')->nullable();
                $table->bigInteger('primary_group_id')->nullable()->unsigned();
                $table->string('remember_token')->nullable();
                $table->text('banned_reason')->nullable();
                $table->timestamp('banned_at')->nullable();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
            });

            Db::update("update users set first_name=name, last_name=surname");
        }

        if (Schema::hasTable('rainlab_user_mail_blockers')) {
            $emails = Db::table('rainlab_user_mail_blockers')->where('template', '*')->pluck('email');
            foreach ($emails->chunk(100) as $chunks) {
                Db::table('users')->whereIn('email', $chunks)->update(['is_mail_blocked' => true]);
            }
        }
    }

    public function down()
    {
    }
};
