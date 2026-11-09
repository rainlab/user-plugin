<?php

use Backend\Widgets\Lists;
use Backend\Models\User as BackendUser;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;

/**
 * UserListSearchTest covers the toolbar search of the Users and Timelines lists, which
 * find a user by their first name, last name or email
 */
class UserListSearchTest extends PluginTestCase
{
    public function testUsersListSearch()
    {
        $sam = $this->createTestUser('Sam', 'Georges', 'sam@example.tld');
        $this->createTestUser('Alex', 'Smith', 'alex@example.tld');

        $this->assertSame([$sam->id], $this->search(new User, 'user/columns.yaml', 'sam'));
        $this->assertSame([$sam->id], $this->search(new User, 'user/columns.yaml', 'georges'));
        $this->assertSame([$sam->id], $this->search(new User, 'user/columns.yaml', 'sam@example.tld'));
        $this->assertSame([], $this->search(new User, 'user/columns.yaml', 'nobody'));
    }

    public function testTimelineSearch()
    {
        $sam = $this->createTestUser('Sam', 'Georges', 'sam@example.tld');
        $alex = $this->createTestUser('Alex', 'Smith', 'alex@example.tld');

        UserLog::createRecord($sam->id, UserLog::TYPE_SELF_LOGIN);
        UserLog::createRecord($alex->id, UserLog::TYPE_SELF_LOGIN);

        $found = function($term) {
            $ids = $this->search(new UserLog, 'userlog/columns-global.yaml', $term);
            return UserLog::whereIn('id', $ids)->pluck('user_id')->unique()->values()->all();
        };

        $this->assertSame([$sam->id], $found('sam'));
        $this->assertSame([$sam->id], $found('georges'));
        $this->assertSame([$sam->id], $found('sam@example.tld'));
        $this->assertSame([], $found('nobody'));
    }

    /**
     * search returns the IDs a list built from the plugin's column definitions finds
     */
    protected function search($model, string $columnsFile, string $term): array
    {
        // Lists load the admin's preferences for the list
        $admin = new BackendUser;
        $admin->is_superuser = true;
        BackendAuth::setUser($admin);

        $list = new Lists(null, [
            'model' => $model,
            'arrayName' => 'array',
            'columns' => Yaml::parseFile(plugins_path('rainlab/user/models/'.$columnsFile))['columns'],
        ]);

        $list->setSearchTerm($term);

        return $list->prepareQuery()->pluck($model->getTable().'.id')->all();
    }

    /**
     * createTestUser is a helper to create a registered user
     */
    protected function createTestUser(string $firstName, string $lastName, string $email): User
    {
        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);
    }
}
