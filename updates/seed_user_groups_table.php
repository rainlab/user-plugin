<?php namespace RainLab\User\Updates;

use RainLab\User\Models\UserGroup;
use October\Rain\Database\Updates\Seeder;

class SeedUserGroupsTable extends Seeder
{
    public function run()
    {
        UserGroup::create([
            'name' => 'Sample group',
            'code' => 'sample',
            'description' => 'Sample group for website users.'
        ]);
    }
}
