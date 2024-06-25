<?php

use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'activation_code')) {
            Schema::table('users', function(Blueprint $table) {
                $table->string('activation_code')->nullable()->index();
            });
        }
    }

    public function down()
    {
    }
};
