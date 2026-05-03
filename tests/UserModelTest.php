<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use RainLab\User\Models\UserLog;

/**
 * UserModelTest covers the User model's core functionality
 */
class UserModelTest extends PluginTestCase
{
    /**
     * createTestUser is a helper to create a registered user
     */
    protected function createTestUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides));
    }

    /**
     * createGuestUser is a helper to create a guest user
     */
    protected function createGuestUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Guest',
            'last_name' => 'User',
            'email' => 'guest@example.tld',
            'is_guest' => true,
        ], $overrides));
    }

    //
    // Attributes
    //

    public function testFullNameAttribute()
    {
        $user = $this->createTestUser(['first_name' => 'John', 'last_name' => 'Doe']);
        $this->assertEquals('John Doe', $user->full_name);
    }

    public function testFullNameTrimsWhitespace()
    {
        $user = $this->createTestUser(['first_name' => 'John', 'last_name' => '']);
        $this->assertEquals('John', $user->full_name);
    }

    public function testIsBannedAttributeWhenNotBanned()
    {
        $user = $this->createTestUser();
        $this->assertFalse($user->is_banned);
    }

    public function testIsActivatedAttributeWhenNotActivated()
    {
        $user = $this->createTestUser();
        $this->assertFalse($user->is_activated);
    }

    public function testIsActivatedAttributeWhenActivated()
    {
        Event::forget('rainlab.user.activate');

        $user = $this->createTestUser();
        $user->markEmailAsVerified();

        $this->assertTrue($user->is_activated);
    }

    public function testPasswordIsHashed()
    {
        $user = $this->createTestUser();
        $this->assertNotEquals('ChangeMe888', $user->password);
        $this->assertTrue(Hash::check('ChangeMe888', $user->password));
    }

    public function testPasswordCannotBeSetToEmptyOnExistingUser()
    {
        $user = $this->createTestUser();
        $originalPassword = $user->password;

        $user->password = '';
        $user->save(['force' => true]);

        $user->refresh();
        $this->assertEquals($originalPassword, $user->password);
    }

    //
    // Banning
    //

    public function testBanUser()
    {
        $user = $this->createTestUser();
        $user->ban('Spamming');

        $this->assertTrue($user->is_banned);
        $this->assertEquals('Spamming', $user->banned_reason);
        $this->assertNotNull($user->banned_at);
    }

    public function testBanUserWithoutReason()
    {
        $user = $this->createTestUser();
        $user->ban();

        $this->assertTrue($user->is_banned);
        $this->assertNull($user->banned_reason);
    }

    public function testUnbanUser()
    {
        $user = $this->createTestUser();
        $user->ban('Spamming');
        $user->unban();

        $this->assertFalse($user->is_banned);
        $this->assertNull($user->banned_reason);
        $this->assertNull($user->banned_at);
    }

    public function testBanDoesNothingIfAlreadyBanned()
    {
        $user = $this->createTestUser();
        $user->ban('First reason');
        $bannedAt = $user->banned_at;

        $user->ban('Second reason');
        $this->assertEquals('First reason', $user->banned_reason);
        $this->assertEquals($bannedAt, $user->banned_at);
    }

    public function testUnbanDoesNothingIfNotBanned()
    {
        $user = $this->createTestUser();
        $user->unban();

        $this->assertFalse($user->is_banned);
    }

    //
    // Groups
    //

    public function testNewUserGetsRegisteredPrimaryGroup()
    {
        $user = $this->createTestUser();
        $registeredGroup = UserGroup::getRegisteredGroup();

        $this->assertEquals($registeredGroup->id, $user->primary_group_id);
    }

    public function testGuestUserGetsGuestPrimaryGroup()
    {
        $guest = $this->createGuestUser();
        $guestGroup = UserGroup::getGuestGroup();

        $this->assertEquals($guestGroup->id, $guest->primary_group_id);
    }

    public function testAddGroupByModel()
    {
        $user = $this->createTestUser();

        $group = UserGroup::create([
            'name' => 'VIP Members',
            'code' => 'vip',
        ]);

        $user->addGroup($group);

        $this->assertTrue($user->inGroup($group, false));
    }

    public function testAddGroupByCode()
    {
        $user = $this->createTestUser();

        $group = UserGroup::create([
            'name' => 'Premium',
            'code' => 'premium',
        ]);

        $user->addGroup('premium');
        $this->assertTrue($user->inGroup('premium', false));
    }

    public function testRemoveGroup()
    {
        $user = $this->createTestUser();

        $group = UserGroup::create([
            'name' => 'Temp Group',
            'code' => 'temp',
        ]);

        $user->addGroup($group);
        $this->assertTrue($user->inGroup($group, false));

        $user->removeGroup($group);
        $this->assertFalse($user->inGroup($group, false));
    }

    public function testRemoveGroupByCode()
    {
        $user = $this->createTestUser();

        UserGroup::create([
            'name' => 'Removable',
            'code' => 'removable',
        ]);

        $user->addGroup('removable');
        $user->removeGroup('removable');
        $this->assertFalse($user->inGroup('removable', false));
    }

    public function testInGroupChecksPrimaryGroup()
    {
        $user = $this->createTestUser();
        $registeredGroup = UserGroup::getRegisteredGroup();

        // With primary check enabled (default)
        $this->assertTrue($user->inGroup($registeredGroup));

        // Without primary check
        $this->assertFalse($user->inGroup($registeredGroup, false));
    }

    public function testInGroupReturnsFalseForInvalidCode()
    {
        $user = $this->createTestUser();
        $this->assertFalse($user->inGroup('nonexistent'));
    }

    public function testAddGroupDoesNotDuplicate()
    {
        $user = $this->createTestUser();

        $group = UserGroup::create([
            'name' => 'Single',
            'code' => 'single',
        ]);

        $user->addGroup($group);
        $user->addGroup($group);

        $this->assertEquals(1, $user->groups()->where('user_group_id', $group->id)->count());
    }

    //
    // Guest conversion
    //

    public function testConvertGuestToRegistered()
    {
        $guest = $this->createGuestUser();

        $this->assertTrue($guest->is_guest);
        $this->assertEquals(UserGroup::getGuestGroup()->id, $guest->primary_group_id);

        $guest->convertToRegistered(false);

        $this->assertFalse($guest->is_guest);
        $this->assertEquals(UserGroup::getRegisteredGroup()->id, $guest->primary_group_id);
    }

    public function testConvertRegisteredUserDoesNothing()
    {
        $user = $this->createTestUser();
        $originalGroupId = $user->primary_group_id;

        $user->convertToRegistered(false);

        $this->assertFalse((bool) $user->is_guest);
        $this->assertEquals($originalGroupId, $user->primary_group_id);
    }

    //
    // Merging
    //

    public function testMergeUser()
    {
        $leading = $this->createTestUser(['email' => 'leading@example.tld']);
        $merged = $this->createTestUser(['email' => 'merged@example.tld']);

        // Create a log on the merged user
        UserLog::createRecord($merged->id, UserLog::TYPE_SELF_LOGIN);

        $leading->mergeUser($merged);

        // Merged user's logs should be reassigned
        $this->assertEquals(0, UserLog::where('user_id', $merged->id)->count());
        $this->assertGreaterThan(0, UserLog::where('user_id', $leading->id)->count());

        // Merged user should be deleted
        $this->assertNull(User::find($merged->id));
        $this->assertNull(User::withTrashed()->find($merged->id));
    }

    public function testMergeUserWithSelfThrowsException()
    {
        $user = $this->createTestUser();

        $this->expectException(\ApplicationException::class);
        $user->mergeUser($user);
    }

    public function testMergeUserFiresEvent()
    {
        $leading = $this->createTestUser(['email' => 'leading@example.tld']);
        $merged = $this->createTestUser(['email' => 'merged@example.tld']);

        $eventFired = false;
        Event::listen('rainlab.user.mergeUser', function ($leadingUser, $mergedUser) use (&$eventFired) {
            $eventFired = true;
        });

        $leading->mergeUser($merged);

        $this->assertTrue($eventFired);
    }

    //
    // Online status
    //

    public function testIsOnlineReturnsFalseWhenNeverSeen()
    {
        $user = $this->createTestUser();
        $this->assertFalse($user->isOnline());
    }

    public function testIsOnlineReturnsTrueWhenRecentlySeen()
    {
        $user = $this->createTestUser();
        $user->last_seen = now();

        $this->assertTrue($user->isOnline());
    }

    public function testIsOnlineReturnsFalseWhenSeenOverFiveMinutesAgo()
    {
        $user = $this->createTestUser();
        $user->last_seen = now()->subMinutes(6);

        $this->assertFalse($user->isOnline());
    }

    public function testTouchLastSeenUpdatesTimestamp()
    {
        $user = $this->createTestUser();
        $this->assertNull($user->last_seen);

        $user->touchLastSeen();

        $user->refresh();
        $this->assertNotNull($user->last_seen);
    }

    public function testTouchLastSeenDoesNotUpdateWhenOnline()
    {
        $user = $this->createTestUser();
        $user->last_seen = $lastSeen = now();
        $user->save(['force' => true]);

        $user->touchLastSeen();

        $user->refresh();
        $this->assertEquals($lastSeen->format('Y-m-d H:i'), $user->last_seen->format('Y-m-d H:i'));
    }

    public function testTouchIpAddress()
    {
        $user = $this->createTestUser();
        $user->touchIpAddress('192.168.1.1');

        $user->refresh();
        $this->assertEquals('192.168.1.1', $user->last_ip_address);
    }

    //
    // Username generation
    //

    public function testUsernameAutoGeneratedFromEmail()
    {
        $user = $this->createTestUser(['email' => 'john@example.tld']);
        $this->assertEquals('john@example.tld', $user->username);
    }

    //
    // Validation
    //

    public function testRequiresFirstName()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        User::create([
            'first_name' => '',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);
    }

    public function testRequiresEmail()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        User::create([
            'first_name' => 'Test',
            'email' => '',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);
    }

    public function testGuestsCanShareEmail()
    {
        $guest1 = $this->createGuestUser(['email' => 'shared@example.tld']);
        $guest2 = $this->createGuestUser(['email' => 'shared@example.tld']);

        $this->assertEquals($guest1->email, $guest2->email);
        $this->assertEquals(2, User::where('email', 'shared@example.tld')->count());
    }

    public function testRequiresPasswordConfirmation()
    {
        $this->expectException(\October\Rain\Database\ModelException::class);

        User::create([
            'first_name' => 'Test',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'DifferentPass999',
        ]);
    }

    //
    // Persist code
    //

    public function testGetPersistCodeGeneratesCode()
    {
        $user = $this->createTestUser();
        $code = $user->getPersistCode();

        $this->assertNotEmpty($code);
        $this->assertEquals(42, strlen($code));
    }

    public function testGetPersistCodeReturnsSameCodeOnSubsequentCalls()
    {
        $user = $this->createTestUser();
        $code1 = $user->getPersistCode();
        $code2 = $user->getPersistCode();

        $this->assertEquals($code1, $code2);
    }

    //
    // Guest auto-password
    //

    public function testGuestUserGetsAutoGeneratedPassword()
    {
        $guest = $this->createGuestUser();
        $this->assertNotEmpty($guest->password);
    }

    //
    // Soft delete
    //

    public function testSoftDeleteUser()
    {
        $user = $this->createTestUser();
        $userId = $user->id;

        $user->delete();

        $this->assertNull(User::find($userId));
        $this->assertNotNull(User::withTrashed()->find($userId));
    }

    public function testRestoreSoftDeletedUser()
    {
        $user = $this->createTestUser();
        $userId = $user->id;

        $user->delete();
        $user->restore();

        $this->assertNotNull(User::find($userId));
    }
}
