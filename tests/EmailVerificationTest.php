<?php

use RainLab\User\Models\User;

/**
 * EmailVerificationTest covers the HasEmailVerification trait
 */
class EmailVerificationTest extends PluginTestCase
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

    public function testNewUserIsNotVerified()
    {
        $user = $this->createTestUser();
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function testMarkEmailAsVerified()
    {
        Event::forget('rainlab.user.activate');

        $user = $this->createTestUser();
        $user->markEmailAsVerified();

        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNotNull($user->activated_at);
    }

    public function testMarkEmailAsVerifiedFiresEvent()
    {
        $eventFired = false;
        Event::listen('rainlab.user.activate', function ($activatedUser) use (&$eventFired) {
            $eventFired = true;
        });

        $user = $this->createTestUser();
        $user->markEmailAsVerified();

        $this->assertTrue($eventFired);
    }

    public function testMarkEmailAsUnverified()
    {
        Event::forget('rainlab.user.activate');

        $user = $this->createTestUser();
        $user->markEmailAsVerified();
        $this->assertTrue($user->hasVerifiedEmail());

        $user->markEmailAsUnverified();
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertNull($user->activated_at);
    }

    public function testGetCodeForEmailVerification()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        $this->assertNotEmpty($code);
        $this->assertStringContainsString('x', $code);

        // Code has three parts: timestamp x user_id x random
        $parts = explode('x', $code, 3);
        $this->assertCount(3, $parts);
        $this->assertEquals($user->id, $parts[1]);
    }

    public function testGetCodeWritesToDatabase()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        $dbUser = User::find($user->id);
        $this->assertEquals($code, $dbUser->activation_code);
    }

    public function testFindUserForEmailVerification()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        $foundUser = User::findUserForEmailVerification($code);

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function testFindUserForEmailVerificationClearsCode()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        User::findUserForEmailVerification($code);

        $dbUser = User::find($user->id);
        $this->assertNull($dbUser->activation_code);
    }

    public function testFindUserForEmailVerificationReturnsNullForInvalidCode()
    {
        $result = User::findUserForEmailVerification('invalid-code');
        $this->assertNull($result);
    }

    public function testFindUserForEmailVerificationReturnsNullForNonStringCode()
    {
        $this->assertNull(User::findUserForEmailVerification(null));
        $this->assertNull(User::findUserForEmailVerification(12345));
    }

    public function testFindUserForEmailVerificationReturnsNullForMalformedCode()
    {
        // Missing parts
        $this->assertNull(User::findUserForEmailVerification('onlyonepart'));
        $this->assertNull(User::findUserForEmailVerification('twoxparts'));
    }

    public function testFindUserForEmailVerificationRejectsExpiredCode()
    {
        $user = $this->createTestUser();

        // Create a code with a timestamp in the past (more than 60 minutes ago)
        $expiredTimestamp = time() - 3700;
        $code = $expiredTimestamp . 'x' . $user->id . 'x' . \Str::random(24);

        $user->newQuery()
            ->where('id', $user->id)
            ->update(['activation_code' => $code]);

        $result = User::findUserForEmailVerification($code);
        $this->assertNull($result);
    }

    public function testFindUserForEmailVerificationRejectsCodeWithWrongUserId()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        // Tamper with the user ID
        $parts = explode('x', $code, 3);
        $parts[1] = '99999';
        $tamperedCode = implode('x', $parts);

        $result = User::findUserForEmailVerification($tamperedCode);
        $this->assertNull($result);
    }

    public function testFindUserForEmailVerificationRejectsCodeNotStoredOnUser()
    {
        $user = $this->createTestUser();

        // Generate a structurally valid code that doesn't match what's stored
        $code = time() . 'x' . $user->id . 'x' . \Str::random(24);

        $result = User::findUserForEmailVerification($code);
        $this->assertNull($result);
    }

    public function testCodeCannotBeReused()
    {
        $user = $this->createTestUser();
        $code = $user->getCodeForEmailVerification();

        // First use should succeed
        $firstResult = User::findUserForEmailVerification($code);
        $this->assertNotNull($firstResult);

        // Second use should fail (code was cleared)
        $secondResult = User::findUserForEmailVerification($code);
        $this->assertNull($secondResult);
    }

    public function testGetEmailForVerification()
    {
        $user = $this->createTestUser(['email' => 'verify@example.tld']);
        $this->assertEquals('verify@example.tld', $user->getEmailForVerification());
    }
}
