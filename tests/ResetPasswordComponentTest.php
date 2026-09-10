<?php

use RainLab\User\Models\User;
use RainLab\User\Components\ResetPassword;

/**
 * ResetPasswordComponentTest covers the ResetPassword component, in particular
 * that completing a password reset verifies the email address (issue #29: a
 * guest who sets their password via the emailed invite link should become an
 * activated, email-verified account).
 */
class ResetPasswordComponentTest extends PluginTestCase
{
    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();

        // The activate flow sends mail and fires events from other plugins
        Event::forget('rainlab.user.activate');
    }

    /**
     * invokeCompletePasswordReset calls the protected completePasswordReset method
     */
    protected function invokeCompletePasswordReset(User $user): void
    {
        $component = new ResetPassword(null, []);

        $method = new ReflectionMethod(ResetPassword::class, 'completePasswordReset');
        $method->setAccessible(true);

        $method->invoke($component, $user);
    }

    /**
     * createUser is a helper to create a registered user
     */
    protected function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'reset@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides));
    }

    /**
     * testCompletingResetVerifiesUnverifiedEmail is the core regression guard for
     * issue #29: a freshly created (unverified) account becomes verified once the
     * emailed password reset is completed.
     */
    public function testCompletingResetVerifiesUnverifiedEmail()
    {
        $user = $this->createUser();
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertNull($user->activated_at);

        $this->invokeCompletePasswordReset($user);

        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertNotNull($user->activated_at);
        $this->assertTrue($user->is_activated);
    }

    /**
     * testCompletingResetLeavesAlreadyVerifiedUserUntouched guards against the
     * "verify on every reset" behaviour: an already-verified user keeps their
     * original activation timestamp and is not re-verified (which would resend
     * the confirmation notification and re-fire the activate event).
     */
    public function testCompletingResetLeavesAlreadyVerifiedUserUntouched()
    {
        $user = $this->createUser();
        $user->markEmailAsVerified();
        $originalActivatedAt = $user->activated_at;

        $this->assertNotNull($originalActivatedAt);

        $reactivated = false;
        Event::listen('rainlab.user.activate', function () use (&$reactivated) {
            $reactivated = true;
        });

        $this->invokeCompletePasswordReset($user);

        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertEquals($originalActivatedAt, $user->activated_at);
        $this->assertFalse($reactivated, 'Already-verified user should not be re-activated on reset.');
    }
}
