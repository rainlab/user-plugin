<?php

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Migration;

/**
 * Backfills the users_groups pivot with each user's primary group so group
 * membership counts and filters include primary-group-only members. New saves
 * keep this in sync via User::afterSave().
 */
return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('users') || !Schema::hasTable('users_groups')) {
            return;
        }

        $rows = DB::table('users')
            ->whereNotNull('primary_group_id')
            ->select('id as user_id', 'primary_group_id as user_group_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('users_groups')
                    ->whereColumn('users_groups.user_id', 'users.id')
                    ->whereColumn('users_groups.user_group_id', 'users.primary_group_id');
            });

        DB::table('users_groups')->insertUsing(['user_id', 'user_group_id'], $rows);
    }

    public function down()
    {
    }
};
