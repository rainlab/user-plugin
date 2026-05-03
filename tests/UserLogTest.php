<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;

/**
 * UserLogTest covers the UserLog model
 */
class UserLogTest extends PluginTestCase
{
    /**
     * createTestUser is a helper to create a registered user
     */
    protected function createTestUser(): User
    {
        return User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);
    }

    public function testCreateRecord()
    {
        $user = $this->createTestUser();

        $log = UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN, [
            'user_agent' => 'PHPUnit',
        ]);

        $this->assertNotNull($log->id);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(UserLog::TYPE_SELF_LOGIN, $log->type);
        $this->assertFalse($log->is_system);
        $this->assertFalse($log->is_comment);
    }

    public function testCreateSystemRecord()
    {
        $user = $this->createTestUser();

        $log = UserLog::createSystemRecord($user->id, UserLog::TYPE_ADMIN_BAN, [
            'reason' => 'Spamming',
        ]);

        $this->assertNotNull($log->id);
        $this->assertEquals(UserLog::TYPE_ADMIN_BAN, $log->type);
        $this->assertTrue($log->is_system);
        $this->assertFalse($log->is_comment);
    }

    public function testCreateSystemComment()
    {
        $user = $this->createTestUser();

        $log = UserLog::createSystemComment($user->id, 'This user needs review.');

        $this->assertNotNull($log->id);
        $this->assertEquals(UserLog::TYPE_INTERNAL_COMMENT, $log->type);
        $this->assertEquals('This user needs review.', $log->comment);
        $this->assertTrue($log->is_comment);
        $this->assertTrue($log->is_system);
    }

    public function testRecordStoresIpAddress()
    {
        $user = $this->createTestUser();

        $log = UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN);

        $this->assertArrayHasKey('ip_address', $log->data);
    }

    public function testRecordStoresCustomData()
    {
        $user = $this->createTestUser();

        $log = UserLog::createRecord($user->id, UserLog::TYPE_SET_EMAIL, [
            'old_email' => 'old@example.tld',
            'new_email' => 'new@example.tld',
        ]);

        $this->assertEquals('old@example.tld', $log->old_email);
        $this->assertEquals('new@example.tld', $log->new_email);
    }

    public function testLogBelongsToUser()
    {
        $user = $this->createTestUser();
        $log = UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN);

        $logUser = $log->user;

        $this->assertNotNull($logUser);
        $this->assertEquals($user->id, $logUser->id);
    }

    public function testLogBelongsToSoftDeletedUser()
    {
        $user = $this->createTestUser();
        $log = UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN);

        $user->delete();

        $log->unsetRelation('user');
        $logUser = $log->user;

        $this->assertNotNull($logUser);
        $this->assertEquals($user->id, $logUser->id);
    }

    public function testFilterTypeOptionsReturnsAllTypes()
    {
        $log = new UserLog;
        $options = $log->filterTypeOptions();

        $this->assertArrayHasKey(UserLog::TYPE_NEW_USER, $options);
        $this->assertArrayHasKey(UserLog::TYPE_SELF_LOGIN, $options);
        $this->assertArrayHasKey(UserLog::TYPE_ADMIN_BAN, $options);
        $this->assertArrayHasKey(UserLog::TYPE_ADMIN_MERGE, $options);
    }

    public function testMultipleLogsForSameUser()
    {
        $user = $this->createTestUser();

        UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN);
        UserLog::createRecord($user->id, UserLog::TYPE_SET_EMAIL);
        UserLog::createSystemRecord($user->id, UserLog::TYPE_ADMIN_BAN);

        $this->assertEquals(3, UserLog::where('user_id', $user->id)->count());
    }

    public function testLogsDeletedWithUser()
    {
        $user = $this->createTestUser();
        UserLog::createRecord($user->id, UserLog::TYPE_SELF_LOGIN);
        UserLog::createRecord($user->id, UserLog::TYPE_SET_EMAIL);

        $this->assertEquals(2, UserLog::where('user_id', $user->id)->count());

        $user->forceDelete();

        $this->assertEquals(0, UserLog::where('user_id', $user->id)->count());
    }
}
