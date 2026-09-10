<?php

use RainLab\User\Models\User;
use RainLab\User\Models\Setting;

/**
 * AuthManagerTest
 */
class AuthManagerTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Setting::clearInternalCache();
    }

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

    /**
     * testLoginAfterRegisteringWithSharedGuestEmail verifies the scenario reported
     * in rainlab/user-plugin#544: after a guest exists for an email, a real
     * registration succeeds and credential lookup resolves to the registered
     * account, not the guest.
     */
    public function testLoginAfterRegisteringWithSharedGuestEmail()
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

        $this->assertEquals(2, User::where('email', 'person@acme.tld')->count());
        $this->assertTrue($guest->is_guest);
        $this->assertFalse((bool) $registered->is_guest);

        $found = Auth::attempt([
            'email' => 'person@acme.tld',
            'password' => 'ChangeMe888',
        ]);

        $this->assertTrue($found);
        $this->assertEquals($registered->id, Auth::user()->id);
    }

    /**
     * makeSignInUser is a helper to create a verified, approved user
     */
    protected function makeSignInUser(array $overrides = []): User
    {
        $user = User::create(array_merge([
            'first_name' => 'Some',
            'email' => 'signin@website.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides));

        $user->markEmailAsVerified();

        return $user->fresh();
    }

    public function testLoginBlockedWhenActivationRequiredAndUnverified()
    {
        Setting::set('require_activation', true);

        $user = User::create([
            'first_name' => 'Some',
            'email' => 'unverified@website.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Auth::login($user);
    }

    public function testLoginAllowedWhenActivationRequiredAndVerified()
    {
        Setting::set('require_activation', true);

        $user = $this->makeSignInUser();

        Auth::login($user);

        $this->assertTrue(Auth::check());
    }

    public function testLoginBlockedWhenApprovalRequiredAndUnapproved()
    {
        Setting::set('require_approval', true);

        $user = $this->makeSignInUser();
        $user->unapprove();

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        Auth::login($user->fresh());
    }

    public function testLoginAllowedWhenApprovalRequiredAndApproved()
    {
        Setting::set('require_approval', true);

        $user = $this->makeSignInUser();
        $user->approve();

        Auth::login($user->fresh());

        $this->assertTrue(Auth::check());
    }
}
