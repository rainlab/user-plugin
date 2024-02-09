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
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->index();
            $table->string('email')->unique();
            $table->mediumText('notes')->nullable();
            $table->integer('role_id')->nullable()->unsigned();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->string('created_ip_address')->nullable();
            $table->string('last_ip_address')->nullable();
            $table->boolean('is_banned')->default(false);
            $table->text('banned_reason')->default(false);
            $table->boolean('is_activated')->default(false);
            $table->timestamp('activated_at')->nullable(); // email_verified_at
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('last_seen')->nullable();
            $table->timestamp('banned_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
