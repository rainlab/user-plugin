<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        $columnsToPrune = [
            'name',
            'surname',
            'activation_code',
            'persist_code',
            'reset_password_code',
            'permissions',
            'is_superuser',
            'is_activated',
        ];

        foreach ($columnsToPrune as $column) {
            if (Schema::hasColumn('users', $column)) {
                Schema::table('users', function(Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    public function down()
    {
    }
};
