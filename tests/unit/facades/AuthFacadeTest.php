<?php namespace RainLab\User\Tests\Unit\Facades;

use Auth;
use RainLab\User\Models\User;
use RainLab\User\Tests\PluginTestCase;

class AuthFacadeTest extends PluginTestCase
{
    public function test_registering_a_user()
    {
        // register a user
        $user = Auth::register([
            'name' => 'Some User',
            'email' => 'some@website.tld',
            'password' => 'changeme',
            'password_confirmation' => 'changeme',
        ]);

        // our one user should be returned
        $this->assertEquals(1, User::count());
        $this->assertInstanceOf('RainLab\User\Models\User', $user);
        
        // and that user should have the following data
        $this->assertEquals('Some User', $user->name);
        $this->assertEquals('some@website.tld', $user->email);
    }
}