<?php

use October\Rain\Database\Updates\Migration;

/**
 * Adds the IP address columns to installs upgraded from before v1.5.1, whose
 * update script was removed when the version history was consolidated. Fresh
 * installs receive them from the base schema.
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        foreach (['created_ip_address', 'last_ip_address'] as $column) {
            if (Schema::hasColumn('users', $column)) {
                continue;
            }

            Schema::table('users', function ($table) use ($column) {
                $table->string($column)->nullable();
            });
        }
    }

    public function down()
    {
    }
};
