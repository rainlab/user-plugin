<?php namespace RainLab\User\Tests\Unit\Facades;

use Auth;
use Event;
use RainLab\User\Models\User;
use RainLab\User\Tests\UserPluginTestCase;

class AuthFacadeTest extends UserPluginTestCase
{
    public function testRegisterUser()
    {
        $user = Auth::register([
            'name' => 'Some User',
            'email' => 'some@website.tld',
            'password' => 'changeme',
            'password_confirmation' => 'changeme',
        ]);

        $this->assertEquals(1, User::count());
        $this->assertInstanceOf(\RainLab\User\Models\User::class, $user);

        $this->assertFalse($user->is_activated);
        $this->assertEquals('Some User', $user->name);
        $this->assertEquals('some@website.tld', $user->email);
    }

    public function testRegisterUserWithAutoActivation()
    {
        // Stop activation events from other plugins
        Event::forget('rainlab.user.activate');

        $user = Auth::register([
            'name' => 'Some User',
            'email' => 'some@website.tld',
            'password' => 'changeme',
            'password_confirmation' => 'changeme',
        ], true);

        $this->assertTrue($user->is_activated);

        $this->assertTrue(Auth::check());
    }

    public function testRegisterGuest()
    {
        $guest = Auth::registerGuest(['email' => 'person@acme.tld']);

        $this->assertEquals(1, User::count());
        $this->assertInstanceOf(\RainLab\User\Models\User::class, $guest);

        $this->assertTrue($guest->is_guest);
        $this->assertEquals('person@acme.tld', $guest->email);
    }

    public function testLoginAndCheckAuth()
    {
        $this->assertFalse(Auth::check());

        $user = User::create([
            'name' => 'Some User',
            'email' => 'some@website.tld',
            'password' => 'changeme',
            'password_confirmation' => 'changeme',
        ]);

        $user->is_activated = true;
        $user->activated_at = now();
        $user->save();

        Auth::login($user);

        $this->assertTrue(Auth::check());
    }
}
