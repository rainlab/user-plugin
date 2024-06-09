<?php

use RainLab\User\Models\User;

/**
 * AuthManagerTest
 */
class AuthManagerTest extends PluginTestCase
{
    /**
     * testRegisterUser
     */
    public function testRegisterUser()
    {
        $user = User::create([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'some@website.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $this->assertEquals(1, User::count());
        $this->assertInstanceOf(User::class, $user);

        $this->assertFalse($user->is_activated);
        $this->assertEquals('Some User', $user->full_name);
        $this->assertEquals('some@website.tld', $user->email);
    }

    /**
     * testRegisterUserWithAutoActivation
     */
    public function testRegisterUserWithAutoActivation()
    {
        // Stop activation events from other plugins
        Event::forget('rainlab.user.activate');

        $user = User::create([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'some@website.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $user->markEmailAsVerified();
        Auth::loginQuietly($user);

        $this->assertTrue($user->is_activated);
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNotNull(Auth::user());
    }

    /**
     * testRegisterGuest
     */
    public function testRegisterGuest()
    {
        $guest = User::create([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'person@acme.tld',
            'is_guest' => true
        ]);

        $this->assertEquals(1, User::count());
        $this->assertInstanceOf(User::class, $guest);

        $this->assertTrue($guest->is_guest);
        $this->assertEquals('person@acme.tld', $guest->email);

        $secondGuest = User::create([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'person@acme.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
            'is_guest' => true
        ]);

        $this->assertEquals(2, User::count());
        $this->assertInstanceOf(User::class, $secondGuest);
        $this->assertTrue($guest->is_guest);
        $this->assertEquals('person@acme.tld', $guest->email);

        $firstGuest = User::where('email', 'person@acme.tld')->first();
        $firstGuest->convertToRegistered(false);

        $this->assertEquals('person@acme.tld', $firstGuest->email);
        $this->assertFalse($firstGuest->is_guest);
    }

    /**
     * testLoginAndCheckAuth
     */
    public function testLoginAndCheckAuth()
    {
        $this->assertFalse(Auth::check());

        $user = User::create([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'some@website.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $user->markEmailAsVerified();

        Auth::login($user);

        $this->assertTrue(Auth::check());
    }
}
