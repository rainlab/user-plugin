<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        $columnsToPrune = [
            'name',
            'last_name',
            'activation_code',
            'reset_password_code',
            'permissions',
            'is_superuser',
            'is_activated',
            'last_login',
        ];

        foreach ($columnsToPrune as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function(Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }

        Schema::dropIfExists('rainlab_user_mail_blockers');
    }

    public function down()
    {
    }
};
