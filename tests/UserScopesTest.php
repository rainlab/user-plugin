<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;

/**
 * UserScopesTest covers the HasModelScopes trait
 */
class UserScopesTest extends PluginTestCase
{
    /**
     * createTestUser is a helper to create a registered user
     */
    protected function createTestUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test' . uniqid() . '@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides));
    }

    //
    // applyRegistered
    //

    public function testApplyRegisteredExcludesGuests()
    {
        $registered = $this->createTestUser();
        $guest = User::create([
            'first_name' => 'Guest',
            'email' => 'guest@example.tld',
            'is_guest' => true,
        ]);

        $results = User::applyRegistered()->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($registered->id, $ids);
        $this->assertNotContains($guest->id, $ids);
    }

    //
    // applyStatusCode
    //

    public function testApplyStatusCodeActive()
    {
        Event::forget('rainlab.user.activate');

        $active = $this->createTestUser(['email' => 'active@example.tld']);
        $active->markEmailAsVerified();

        $inactive = $this->createTestUser(['email' => 'inactive@example.tld']);

        $results = User::applyStatusCode('active')->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($active->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    public function testApplyStatusCodeInactive()
    {
        Event::forget('rainlab.user.activate');

        $active = $this->createTestUser(['email' => 'active@example.tld']);
        $active->markEmailAsVerified();

        $inactive = $this->createTestUser(['email' => 'inactive@example.tld']);

        $results = User::applyStatusCode('inactive')->get();
        $ids = $results->pluck('id')->all();

        $this->assertNotContains($active->id, $ids);
        $this->assertContains($inactive->id, $ids);
    }

    public function testApplyStatusCodeDeleted()
    {
        $user = $this->createTestUser();
        $userId = $user->id;
        $user->delete();

        $results = User::applyStatusCode('deleted')->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($userId, $ids);
    }

    public function testApplyStatusCodeDeletedExcludesNonTrashed()
    {
        $activeUser = $this->createTestUser(['email' => 'active@example.tld']);
        $deletedUser = $this->createTestUser(['email' => 'deleted@example.tld']);
        $deletedUser->delete();

        $results = User::applyStatusCode('deleted')->get();
        $ids = $results->pluck('id')->all();

        $this->assertNotContains($activeUser->id, $ids);
        $this->assertContains($deletedUser->id, $ids);
    }

    //
    // isActivated
    //

    public function testScopeIsActivated()
    {
        Event::forget('rainlab.user.activate');

        $active = $this->createTestUser(['email' => 'active@example.tld']);
        $active->markEmailAsVerified();

        $inactive = $this->createTestUser(['email' => 'inactive@example.tld']);

        $results = User::isActivated()->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($active->id, $ids);
        $this->assertNotContains($inactive->id, $ids);
    }

    //
    // applyPrimaryGroup
    //

    public function testApplyPrimaryGroup()
    {
        $user = $this->createTestUser();
        $registeredGroup = UserGroup::getRegisteredGroup();

        $results = User::applyPrimaryGroup($registeredGroup->id)->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($user->id, $ids);
    }

    public function testApplyPrimaryGroupExcludesOtherGroups()
    {
        $user = $this->createTestUser();

        $customGroup = UserGroup::create([
            'name' => 'Custom Group',
            'code' => 'custom-scope-test',
        ]);

        $results = User::applyPrimaryGroup($customGroup->id)->get();
        $ids = $results->pluck('id')->all();

        $this->assertNotContains($user->id, $ids);
    }

    //
    // applyGroups (checks both primary and secondary)
    //

    public function testApplyGroupsMatchesPrimaryGroup()
    {
        $user = $this->createTestUser();
        $registeredGroup = UserGroup::getRegisteredGroup();

        $results = User::applyGroups($registeredGroup->id)->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($user->id, $ids);
    }

    public function testApplyGroupsMatchesSecondaryGroup()
    {
        $user = $this->createTestUser();

        $group = UserGroup::create([
            'name' => 'Secondary',
            'code' => 'secondary-scope',
        ]);
        $user->addGroup($group);

        $results = User::applyGroups($group->id)->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($user->id, $ids);
    }

    public function testApplyGroupsWithArrayOfIds()
    {
        $user1 = $this->createTestUser(['email' => 'user1@example.tld']);
        $user2 = $this->createTestUser(['email' => 'user2@example.tld']);

        $group1 = UserGroup::create(['name' => 'Group A', 'code' => 'group-a']);
        $group2 = UserGroup::create(['name' => 'Group B', 'code' => 'group-b']);

        $user1->addGroup($group1);
        $user2->addGroup($group2);

        $results = User::applyGroups([$group1->id, $group2->id])->get();
        $ids = $results->pluck('id')->all();

        $this->assertContains($user1->id, $ids);
        $this->assertContains($user2->id, $ids);
    }
}
