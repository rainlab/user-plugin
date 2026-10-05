<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use RainLab\User\Models\Setting;
use RainLab\User\Components\Session;

/**
 * SessionComponentTest covers the Session component
 */
class SessionComponentTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Setting::clearInternalCache();

        Request::setLaravelSession(App::make('session.store'));
    }

    /**
     * invokeCheckUserGroupSecurity calls the protected component method
     */
    protected function invokeCheckUserGroupSecurity(Session $component): bool
    {
        $method = new ReflectionMethod(Session::class, 'checkUserGroupSecurity');
        $method->setAccessible(true);

        return $method->invoke($component);
    }

    /**
     * makeComponent constructs the Session component with properties
     */
    protected function makeComponent(array $properties = []): Session
    {
        return new Session(null, $properties);
    }

    /**
     * makeUser creates and authenticates a user
     */
    protected function makeUser(): User
    {
        $user = User::create([
            'first_name' => 'Session',
            'last_name' => 'Tester',
            'email' => 'session@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        Auth::login($user);

        return $user;
    }

    public function testGroupSecurityAllowsAllWhenPropertyEmpty()
    {
        $this->makeUser();

        $component = $this->makeComponent(['allowUserGroups' => []]);

        $this->assertTrue($this->invokeCheckUserGroupSecurity($component));
    }

    public function testGroupSecurityAllowsGuests()
    {
        $component = $this->makeComponent(['allowUserGroups' => ['premium']]);

        $this->assertTrue($this->invokeCheckUserGroupSecurity($component));
    }

    public function testGroupSecurityAllowsUserInGroup()
    {
        $group = UserGroup::create([
            'name' => 'Premium Group',
            'code' => 'premium',
        ]);

        $user = $this->makeUser();
        $user->addGroup($group);

        $component = $this->makeComponent(['allowUserGroups' => ['premium']]);

        $this->assertTrue($this->invokeCheckUserGroupSecurity($component));
    }

    public function testGroupSecurityAllowsUserInPrimaryGroup()
    {
        $group = UserGroup::create([
            'name' => 'Premium Group',
            'code' => 'premium',
        ]);

        $user = $this->makeUser();
        $user->primary_group_id = $group->id;
        $user->save();

        $component = $this->makeComponent(['allowUserGroups' => ['premium']]);

        $this->assertTrue($this->invokeCheckUserGroupSecurity($component));
    }

    public function testGroupSecuritySupportsOriginalPropertyName()
    {
        $group = UserGroup::create([
            'name' => 'Premium Group',
            'code' => 'premium',
        ]);

        $user = $this->makeUser();
        $user->addGroup($group);

        $component = $this->makeComponent(['allowedUserGroups' => ['premium']]);
        $this->assertTrue($this->invokeCheckUserGroupSecurity($component));

        $user->removeGroup($group);
        $user->unsetRelation('groups');

        $component = $this->makeComponent(['allowedUserGroups' => ['premium']]);
        $this->assertFalse($this->invokeCheckUserGroupSecurity($component));
    }

    public function testGroupSecurityDeniesUserNotInGroup()
    {
        UserGroup::create([
            'name' => 'Premium Group',
            'code' => 'premium',
        ]);

        $this->makeUser();

        $component = $this->makeComponent(['allowUserGroups' => ['premium']]);

        $this->assertFalse($this->invokeCheckUserGroupSecurity($component));
    }

    /**
     * invokeAuthenticateSession calls the protected component method
     */
    protected function invokeAuthenticateSession(Session $component): void
    {
        $method = new ReflectionMethod(Session::class, 'authenticateSession');
        $method->setAccessible(true);
        $method->invoke($component);
    }

    public function testAuthenticateSessionStoresHmacWhenMissing()
    {
        $user = $this->makeUser();

        Request::session()->forget(Auth::getPasswordHashName());

        $this->invokeAuthenticateSession($this->makeComponent());

        $this->assertTrue(Auth::check());
        $this->assertEquals(
            Auth::hashPasswordForCookie($user->getAuthPassword()),
            Request::session()->get(Auth::getPasswordHashName())
        );
    }

    public function testAuthenticateSessionKeepsUserWithLaravelLoginHash()
    {
        $user = $this->makeUser();

        Request::session()->put(
            'password_hash_'.Auth::getDefaultDriver(),
            Auth::hashPasswordForCookie($user->getAuthPassword())
        );

        $this->invokeAuthenticateSession($this->makeComponent());

        $this->assertTrue(Auth::check());
    }

    public function testAuthenticateSessionKeepsUserWithRawHash()
    {
        $user = $this->makeUser();

        Request::session()->put(Auth::getPasswordHashName(), $user->getAuthPassword());

        $this->invokeAuthenticateSession($this->makeComponent());

        $this->assertTrue(Auth::check());
    }

    public function testAuthenticateSessionSignsOutWhenPasswordChanged()
    {
        $user = $this->makeUser();

        Request::session()->put(
            Auth::getPasswordHashName(),
            Auth::hashPasswordForCookie($user->getAuthPassword())
        );

        $user->password = 'Different888';
        $user->password_confirmation = 'Different888';
        $user->save();

        $this->invokeAuthenticateSession($this->makeComponent());

        $this->assertFalse(Auth::check());
    }
}
