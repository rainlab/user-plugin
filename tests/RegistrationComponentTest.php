<?php

use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Components\Registration;

/**
 * RegistrationComponentTest covers the Registration component
 */
class RegistrationComponentTest extends PluginTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Setting::clearInternalCache();
    }

    /**
     * invokeCreateNewUser calls the protected createNewUser method
     */
    protected function invokeCreateNewUser(array $input): User
    {
        $component = new Registration(null, []);

        $method = new ReflectionMethod(Registration::class, 'createNewUser');
        $method->setAccessible(true);

        return $method->invoke($component, $input);
    }

    /**
     * validInput returns a baseline valid input payload
     */
    protected function validInput(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Some',
            'last_name' => 'User',
            'email' => 'test@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides);
    }

    public function testCreateNewUserRegistersUser()
    {
        $user = $this->invokeCreateNewUser($this->validInput());

        $this->assertInstanceOf(User::class, $user);
        $this->assertFalse((bool) $user->is_guest);
        $this->assertEquals('test@example.tld', $user->email);
    }

    public function testCreateNewUserAllowsRegistrationWhenGuestExistsWithSameEmail()
    {
        $guest = User::create([
            'first_name' => 'Guest',
            'email' => 'shared@example.tld',
            'is_guest' => true,
        ]);

        $user = $this->invokeCreateNewUser($this->validInput([
            'email' => 'shared@example.tld',
        ]));

        $this->assertTrue($guest->is_guest);
        $this->assertFalse((bool) $user->is_guest);
        $this->assertNotEquals($guest->id, $user->id);
        $this->assertEquals(2, User::where('email', 'shared@example.tld')->count());
    }

    public function testCreateNewUserRejectsDuplicateRegisteredEmail()
    {
        User::create([
            'first_name' => 'Existing',
            'email' => 'taken@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $this->invokeCreateNewUser($this->validInput([
            'email' => 'taken@example.tld',
        ]));
    }

    /**
     * invokeCanSignInAfterRegister calls the protected component method
     */
    protected function invokeCanSignInAfterRegister(User $user): bool
    {
        $component = new Registration(null, []);

        $method = new ReflectionMethod(Registration::class, 'canSignInAfterRegister');
        $method->setAccessible(true);

        return $method->invoke($component, $user);
    }

    public function testUserSignsInWhenNoActivationRequired()
    {
        Setting::set('require_activation', false);
        Setting::set('require_approval', false);

        $user = $this->invokeCreateNewUser($this->validInput());
        $this->assertFalse($user->hasVerifiedEmail());

        // With no activation policy the user signs in immediately
        $this->assertTrue($this->invokeCanSignInAfterRegister($user));
    }

    public function testUnverifiedUserDeferredWhenActivationRequired()
    {
        Setting::set('require_activation', true);
        Setting::set('require_approval', false);

        $user = $this->invokeCreateNewUser($this->validInput());

        $this->assertFalse($this->invokeCanSignInAfterRegister($user));
    }

    public function testVerifiedUserSignsInWhenActivationRequired()
    {
        Setting::set('require_activation', true);
        Setting::set('require_approval', false);

        $user = $this->invokeCreateNewUser($this->validInput());
        $user->markEmailAsVerified();

        $this->assertTrue($this->invokeCanSignInAfterRegister($user));
    }

    public function testUnapprovedUserDeferredWhenApprovalRequired()
    {
        Setting::set('require_activation', false);
        Setting::set('require_approval', true);

        $user = $this->invokeCreateNewUser($this->validInput());
        $user->unapprove();

        $this->assertFalse($this->invokeCanSignInAfterRegister($user->fresh()));
    }

    public function testApprovedUserSignsInWhenApprovalRequired()
    {
        Setting::set('require_activation', false);
        Setting::set('require_approval', true);

        $user = $this->invokeCreateNewUser($this->validInput());
        $user->unapprove();
        $user->approve();

        $this->assertTrue($this->invokeCanSignInAfterRegister($user->fresh()));
    }

    public function testBothPoliciesMustBeSatisfied()
    {
        Setting::set('require_activation', true);
        Setting::set('require_approval', true);

        $user = $this->invokeCreateNewUser($this->validInput());
        $user->unapprove();

        // Verified but not approved is still blocked
        $user->markEmailAsVerified();
        $this->assertFalse($this->invokeCanSignInAfterRegister($user->fresh()));

        // Approved as well allows sign in
        $user->approve();
        $this->assertTrue($this->invokeCanSignInAfterRegister($user->fresh()));
    }
}
