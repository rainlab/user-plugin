<?php namespace RainLab\User\Updates;

use RainLab\User\Models\UserGroup;
use October\Rain\Database\Updates\Seeder;

class SeedUserGroupsTable extends Seeder
{
    public function run()
    {
        UserGroup::create([
            'name' => 'Default group',
            'code' => 'default',
            'description' => 'Default group for website users.',
            'is_new_user_default' => true
        ]);
    }
}
