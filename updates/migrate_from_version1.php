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
    }

    public function down()
    {
    }
};
