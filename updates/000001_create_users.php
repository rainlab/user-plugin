<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('is_guest')->default(false);
            $table->boolean('is_mail_blocked')->default(false);
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->index();
            $table->string('email');
            $table->mediumText('notes')->nullable();
            $table->string('password');
            $table->string('activation_code')->nullable()->index();
            $table->string('persist_code')->nullable();
            $table->string('remember_token')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->bigInteger('primary_group_id')->nullable()->unsigned();
            $table->string('created_ip_address')->nullable();
            $table->string('last_ip_address')->nullable();
            $table->text('banned_reason')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
