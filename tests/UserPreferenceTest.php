<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserPreference;
use Illuminate\Mail\Message;
use Symfony\Component\Mime\Email;

/**
 * UserPreferenceTest covers the UserPreference model
 */
class UserPreferenceTest extends PluginTestCase
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

    /**
     * makeMailMessage is a helper to create a mail message for the given addresses
     */
    protected function makeMailMessage(array $addresses): Message
    {
        $message = new Message(new Email);
        $message->to($addresses);

        return $message;
    }

    /**
     * getMailRecipients is a helper to list the addresses a mail message is sent to
     */
    protected function getMailRecipients(Message $message): array
    {
        return array_values(array_map(fn($address) => $address->getAddress(), $message->getTo()));
    }

    public function setUp(): void
    {
        parent::setUp();

        // Reset the static preference cache between tests
        $reflection = new ReflectionClass(UserPreference::class);
        $property = $reflection->getProperty('preferenceCache');
        $property->setAccessible(true);
        $property->setValue(null, []);
    }

    public function testSetAndGetPreference()
    {
        $user = $this->createTestUser();

        UserPreference::setPreference($user->id, 'theme', 'dark');

        $value = UserPreference::getPreference($user->id, 'theme');
        $this->assertEquals('dark', $value);
    }

    public function testGetPreferenceReturnsDefault()
    {
        $user = $this->createTestUser();

        $value = UserPreference::getPreference($user->id, 'nonexistent', 'fallback');
        $this->assertEquals('fallback', $value);
    }

    public function testGetPreferenceReturnsNullByDefault()
    {
        $user = $this->createTestUser();

        $value = UserPreference::getPreference($user->id, 'nonexistent');
        $this->assertNull($value);
    }

    public function testSetPreferenceToNullDeletesIt()
    {
        $user = $this->createTestUser();

        UserPreference::setPreference($user->id, 'theme', 'dark');
        UserPreference::setPreference($user->id, 'theme', null);

        // Reset cache to ensure we read from DB
        $reflection = new ReflectionClass(UserPreference::class);
        $property = $reflection->getProperty('preferenceCache');
        $property->setAccessible(true);
        $property->setValue(null, []);

        $value = UserPreference::getPreference($user->id, 'theme', 'default');
        $this->assertEquals('default', $value);
    }

    public function testSetPreferences()
    {
        $user = $this->createTestUser();

        UserPreference::setPreferences($user->id, [
            'theme' => 'dark',
            'language' => 'en',
            'notifications' => true,
        ]);

        $this->assertEquals('dark', UserPreference::getPreference($user->id, 'theme'));
        $this->assertEquals('en', UserPreference::getPreference($user->id, 'language'));
        $this->assertTrue(UserPreference::getPreference($user->id, 'notifications'));
    }

    public function testSetPreferenceOverwritesPreviousValue()
    {
        $user = $this->createTestUser();

        UserPreference::setPreference($user->id, 'theme', 'light');
        UserPreference::setPreference($user->id, 'theme', 'dark');

        $this->assertEquals('dark', UserPreference::getPreference($user->id, 'theme'));
    }

    public function testResetAllPreferences()
    {
        $user = $this->createTestUser();

        UserPreference::setPreferences($user->id, [
            'theme' => 'dark',
            'language' => 'en',
        ]);

        UserPreference::resetAll($user->id);

        // Reset cache
        $reflection = new ReflectionClass(UserPreference::class);
        $property = $reflection->getProperty('preferenceCache');
        $property->setAccessible(true);
        $property->setValue(null, []);

        $this->assertNull(UserPreference::getPreference($user->id, 'theme'));
        $this->assertNull(UserPreference::getPreference($user->id, 'language'));
    }

    public function testSetPreferencesSafeSanitizesStrings()
    {
        $user = $this->createTestUser();

        UserPreference::setPreferencesSafe($user->id, [
            'nickname' => 'Hello World!',
        ]);

        $value = UserPreference::getPreference($user->id, 'nickname');
        $this->assertEquals('hello world', $value);
    }

    public function testSetPreferencesSafeHandlesBooleanStrings()
    {
        $user = $this->createTestUser();

        UserPreference::setPreferencesSafe($user->id, [
            'enabled' => 'true',
            'disabled' => 'false',
            'empty' => 'null',
        ]);

        $this->assertTrue(UserPreference::getPreference($user->id, 'enabled'));
        $this->assertFalse(UserPreference::getPreference($user->id, 'disabled'));
        $this->assertNull(UserPreference::getPreference($user->id, 'empty'));
    }

    public function testSetPreferencesSafePreservesNumericValues()
    {
        $user = $this->createTestUser();

        UserPreference::setPreferencesSafe($user->id, [
            'count' => 42,
        ]);

        $this->assertEquals(42, UserPreference::getPreference($user->id, 'count'));
    }

    public function testPreferencesIsolatedPerUser()
    {
        $user1 = $this->createTestUser();
        $user2 = User::create([
            'first_name' => 'Second',
            'last_name' => 'User',
            'email' => 'second@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        UserPreference::setPreference($user1->id, 'theme', 'dark');
        UserPreference::setPreference($user2->id, 'theme', 'light');

        $this->assertEquals('dark', UserPreference::getPreference($user1->id, 'theme'));
        $this->assertEquals('light', UserPreference::getPreference($user2->id, 'theme'));
    }

    public function testSetPreferenceIgnoresNullUserId()
    {
        UserPreference::setPreference(null, 'theme', 'dark');

        // No exception thrown, just silently ignored
        $this->assertTrue(true);
    }

    public function testGetPreferenceReturnsDefaultForNullUserId()
    {
        $value = UserPreference::getPreference(null, 'theme', 'default');
        $this->assertEquals('default', $value);
    }

    public function testUserModelGetPreference()
    {
        $user = $this->createTestUser();

        UserPreference::setPreference($user->id, 'theme', 'dark');

        $this->assertEquals('dark', $user->getPreference('theme'));
        $this->assertEquals('fallback', $user->getPreference('missing', 'fallback'));
    }

    public function testFilterMailMessageRemovesBlockedRecipients()
    {
        $user = $this->createTestUser();
        $user->is_mail_blocked = true;
        $user->forceSave();

        $message = $this->makeMailMessage(['test@example.tld', 'other@example.tld']);

        $this->assertNull(UserPreference::filterMailMessage('user:welcome_email', $message));
        $this->assertEquals(['other@example.tld'], $this->getMailRecipients($message));
    }

    public function testFilterMailMessageCancelsWhenEveryRecipientIsBlocked()
    {
        $user = $this->createTestUser();
        $user->is_mail_blocked = true;
        $user->forceSave();

        $message = $this->makeMailMessage(['test@example.tld']);

        $this->assertFalse(UserPreference::filterMailMessage('user:welcome_email', $message));
    }

    public function testFilterMailMessageSendsSafeTemplatesToBlockedUsers()
    {
        $user = $this->createTestUser();
        $user->is_mail_blocked = true;
        $user->forceSave();

        $message = $this->makeMailMessage(['test@example.tld']);

        $this->assertNull(UserPreference::filterMailMessage('user:recover_password', $message));
        $this->assertEquals(['test@example.tld'], $this->getMailRecipients($message));
    }

    public function testFilterMailMessageLeavesUnblockedRecipients()
    {
        $this->createTestUser();

        $message = $this->makeMailMessage(['test@example.tld']);

        $this->assertNull(UserPreference::filterMailMessage('user:welcome_email', $message));
        $this->assertEquals(['test@example.tld'], $this->getMailRecipients($message));
    }

    public function testPreferenceStoresComplexValues()
    {
        $user = $this->createTestUser();

        UserPreference::setPreference($user->id, 'settings', [
            'notifications' => ['email' => true, 'sms' => false],
            'timezone' => 'UTC',
        ]);

        $value = UserPreference::getPreference($user->id, 'settings');
        $this->assertEquals(true, $value['notifications']['email']);
        $this->assertEquals(false, $value['notifications']['sms']);
        $this->assertEquals('UTC', $value['timezone']);
    }
}
