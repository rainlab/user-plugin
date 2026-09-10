<?php

use RainLab\User\Models\User;
use RainLab\User\Models\UserGroup;
use RainLab\User\Components\Session;

/**
 * SessionComponentTest covers the Session component
 */
class SessionComponentTest extends PluginTestCase
{
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
     * invokeCheckUserActivationSecurity calls the protected component method
     */
    protected function invokeCheckUserActivationSecurity(Session $component): bool
    {
        $method = new ReflectionMethod(Session::class, 'checkUserActivationSecurity');
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

    public function testActivationSecurityAllowsUnactivatedUserByDefault()
    {
        $user = $this->makeUser();
        $this->assertFalse($user->hasVerifiedEmail());

        // Default behavior does not require activation
        $component = $this->makeComponent();

        $this->assertTrue($this->invokeCheckUserActivationSecurity($component));
    }

    public function testActivationSecurityDeniesUnactivatedUserWhenRequired()
    {
        $user = $this->makeUser();
        $this->assertFalse($user->hasVerifiedEmail());

        $component = $this->makeComponent(['requireActivation' => true]);

        $this->assertFalse($this->invokeCheckUserActivationSecurity($component));
    }

    public function testActivationSecurityAllowsActivatedUserWhenRequired()
    {
        $user = $this->makeUser();
        $user->markEmailAsVerified();
        $this->assertTrue($user->hasVerifiedEmail());

        $component = $this->makeComponent(['requireActivation' => true]);

        $this->assertTrue($this->invokeCheckUserActivationSecurity($component));
    }

    public function testActivationSecurityAllowsGuestWhenRequired()
    {
        // No authenticated user, activation check should not block
        $component = $this->makeComponent(['requireActivation' => true]);

        $this->assertTrue($this->invokeCheckUserActivationSecurity($component));
    }
}
