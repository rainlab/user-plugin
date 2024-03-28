<?php namespace RainLab\User\Updates;

use RainLab\User\Models\UserGroup;
use October\Rain\Database\Updates\Seeder;

/**
 * SeedTables for the shop module
 */
class SeedTables extends Seeder
{
    /**
     * run migration
     */
    public function run()
    {
        UserGroup::create([
            'name' => 'Guest',
            'code' => 'guest',
            'description' => 'Default group for guest users.'
        ]);

        UserGroup::create([
            'name' => 'Registered',
            'code' => 'registered',
            'description' => 'Default group for registered users.'
        ]);
    }
}
