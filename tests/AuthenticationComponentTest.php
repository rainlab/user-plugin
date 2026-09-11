<?php

use RainLab\User\Models\User;
use RainLab\User\Classes\ActionManager;
use RainLab\User\Components\Authentication;

/**
 * AuthenticationComponentTest covers the Authentication component
 */
class AuthenticationComponentTest extends PluginTestCase
{
    /**
     * invokeAttemptTwoFactorAuthentication calls the protected trait method
     */
    protected function invokeAttemptTwoFactorAuthentication(array $input)
    {
        $method = new ReflectionMethod(ActionManager::class, 'attemptTwoFactorAuthentication');
        $method->setAccessible(true);

        return $method->invoke(ActionManager::instance(), $input);
    }

    /**
     * makeComponent constructs the Authentication component
     */
    protected function makeComponent(array $properties = []): Authentication
    {
        return new Authentication(null, $properties);
    }

    /**
     * invokeMakeRedirectUrl calls the protected component method
     */
    protected function invokeMakeRedirectUrl(Authentication $component): ?string
    {
        $method = new ReflectionMethod(Authentication::class, 'makeRedirectUrl');
        $method->setAccessible(true);

        return $method->invoke($component);
    }

    public function testRedirectUrlIsNullWhenPropertyEmpty()
    {
        // Preserves the previous behavior where redirect comes only from the form
        $this->assertNull($this->invokeMakeRedirectUrl($this->makeComponent()));
        $this->assertNull($this->invokeMakeRedirectUrl($this->makeComponent(['redirect' => ''])));
    }

    public function testTwoFactorLoginSkipsGuestWhenRegisteredSharesEmail()
    {
        $guest = User::create([
            'first_name' => 'Guest',
            'email' => 'person@acme.tld',
            'is_guest' => true,
        ]);

        $registered = User::create([
            'first_name' => 'Registered',
            'email' => 'person@acme.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $user = $this->invokeAttemptTwoFactorAuthentication([
            'email' => 'person@acme.tld',
            'password' => 'ChangeMe888',
        ]);

        $this->assertNotNull($user);
        $this->assertEquals($registered->id, $user->id);
        $this->assertFalse((bool) $user->is_guest);
    }
}
