<?php

use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Classes\ActionManager;
use RainLab\User\Classes\TwoFactorManager;

/**
 * ActionManagerTest covers headless usage of the user workflows, where no CMS
 * component is present, as used by REST or GraphQL integrations
 */
class ActionManagerTest extends PluginTestCase
{
    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();

        Setting::clearInternalCache();
    }

    /**
     * validInput returns a baseline valid registration payload
     */
    protected function validInput(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Head',
            'last_name' => 'Less',
            'email' => 'headless@example.tld',
            'password' => 'ChangeMe888',
            'password_confirmation' => 'ChangeMe888',
        ], $overrides);
    }

    public function testRegisterUserSignsInImmediately()
    {
        $user = ActionManager::instance()->registerUser($this->validInput());

        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue(Auth::check());
        $this->assertEquals('headless@example.tld', Auth::user()->email);
    }

    public function testRegisterUserBlockedWhenRegistrationDisabled()
    {
        Setting::set('allow_registration', false);

        $this->expectException(NotFoundException::class);

        ActionManager::instance()->registerUser($this->validInput());
    }

    public function testRegisterUserDefersSignInWhenApprovalRequired()
    {
        Setting::set('require_approval', true);

        $user = ActionManager::instance()->registerUser($this->validInput());

        $this->assertTrue($user->isPendingApproval());
        $this->assertFalse(Auth::check());
        $this->assertFalse(ActionManager::instance()->canSignInAfterRegister($user));
    }

    public function testRegisterEventReceivesManagerWithoutContext()
    {
        $context = null;
        Event::listen('rainlab.user.register', function ($emitter, $user) use (&$context) {
            $context = $emitter;
        });

        ActionManager::instance()->registerUser($this->validInput());

        $this->assertInstanceOf(ActionManager::class, $context);
    }

    public function testLoginAuthenticatesUser()
    {
        $user = ActionManager::instance()->registerUser($this->validInput());
        Auth::logout();
        $this->assertFalse(Auth::check());

        ActionManager::instance()->login([
            'email' => 'headless@example.tld',
            'password' => 'ChangeMe888',
        ]);

        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::user()->getKey());
    }

    public function testRecoverPasswordUsesCustomResetUrl()
    {
        ActionManager::instance()->registerUser($this->validInput());
        Auth::logout();

        $captured = null;
        Event::listen('mailer.beforeSend', function ($view, $data) use (&$captured) {
            if ($view === 'user:recover_password') {
                $captured = $data;
                return false;
            }
        });

        ActionManager::instance()->recoverPassword([
            'email' => 'headless@example.tld',
        ], [
            'resetUrl' => 'https://api.example.tld/reset-password',
        ]);

        $this->assertNotNull($captured);
        $this->assertStringStartsWith('https://api.example.tld/reset-password?', $captured['url']);
        $this->assertStringContainsString('email=headless%40example.tld', $captured['url']);
    }

    public function testLogoutSignsOutUser()
    {
        ActionManager::instance()->registerUser($this->validInput());
        $this->assertTrue(Auth::check());

        ActionManager::instance()->logout();

        $this->assertFalse(Auth::check());
    }

    public function testDeleteOtherSessionsKeepsCurrentDeviceSignedIn()
    {
        $user = ActionManager::instance()->registerUser($this->validInput());

        ActionManager::instance()->deleteOtherSessions(['password' => 'ChangeMe888']);

        $this->assertTrue(Hash::check('ChangeMe888', $user->fresh()->password));
        $this->assertTrue(Auth::hasValidPasswordHash($user->fresh()));
    }

    /**
     * startTwoFactorChallenge registers a two factor user and begins a challenge where 424242 is the valid code
     */
    protected function startTwoFactorChallenge(): User
    {
        App::instance('user.twofactor', new class extends TwoFactorManager {
            public function __construct()
            {
            }

            public function generateSecretKey(): string
            {
                return 'TESTSECRET';
            }

            public function verify(string $secret, string $code): bool
            {
                return $code === '424242';
            }
        });

        $user = User::where('email', 'headless@example.tld')->first();

        if (!$user) {
            $user = ActionManager::instance()->registerUser($this->validInput());
            $user->enableTwoFactorAuthentication();
            $user->forceFill(['two_factor_confirmed_at' => $user->freshTimestamp()])->save();
        }

        Auth::logout();

        $result = ActionManager::instance()->loginWithTwoFactor([
            'email' => 'headless@example.tld',
            'password' => 'ChangeMe888',
        ]);

        $this->assertEquals(ActionManager::TWO_FACTOR_CHALLENGE, $result);
        $this->assertFalse(Auth::check());

        return $user;
    }

    /**
     * attemptTwoFactorChallenge returns the validation message of a failed challenge, or null when it passes
     */
    protected function attemptTwoFactorChallenge(array $input): ?string
    {
        try {
            ActionManager::instance()->twoFactorChallenge($input);
        }
        catch (ValidationException $ex) {
            return $ex->getMessage();
        }

        return null;
    }

    public function testTwoFactorChallengeSignsInWithValidCode()
    {
        $user = $this->startTwoFactorChallenge();

        $this->assertStringContainsString('code was invalid', $this->attemptTwoFactorChallenge(['code' => '000000']));
        $this->assertFalse(Auth::check());

        $this->assertNull($this->attemptTwoFactorChallenge(['code' => '424242']));
        $this->assertTrue(Auth::check());
        $this->assertEquals($user->id, Auth::user()->getKey());
    }

    public function testTwoFactorChallengeThrottlesFailedAttempts()
    {
        $this->startTwoFactorChallenge();

        for ($i = 0; $i < 5; $i++) {
            $this->assertStringContainsString('code was invalid', $this->attemptTwoFactorChallenge(['code' => '00000'.$i]));
        }

        $this->assertStringContainsString('Too many login attempts', $this->attemptTwoFactorChallenge(['code' => '424242']));
        $this->assertFalse(Auth::check());
    }

    public function testTwoFactorChallengeThrottlesRecoveryCodes()
    {
        $this->startTwoFactorChallenge();

        for ($i = 0; $i < 5; $i++) {
            $this->assertStringContainsString('recovery code was invalid', $this->attemptTwoFactorChallenge(['recovery_code' => 'wrong-'.$i]));
        }

        $this->assertStringContainsString('Too many login attempts', $this->attemptTwoFactorChallenge(['recovery_code' => 'wrong-again']));
        $this->assertFalse(Auth::check());
    }

    public function testTwoFactorChallengeThrottleHoldsAcrossIpAddresses()
    {
        $this->startTwoFactorChallenge();

        for ($i = 0; $i < 5; $i++) {
            Request::instance()->server->set('REMOTE_ADDR', '10.0.0.'.$i);
            $this->attemptTwoFactorChallenge(['code' => '00000'.$i]);
        }

        Request::instance()->server->set('REMOTE_ADDR', '10.0.0.99');

        $this->assertStringContainsString('Too many login attempts', $this->attemptTwoFactorChallenge(['code' => '424242']));
        $this->assertFalse(Auth::check());
    }

    public function testTwoFactorChallengeClearsAttemptsOnSuccess()
    {
        $this->startTwoFactorChallenge();

        for ($i = 0; $i < 4; $i++) {
            $this->attemptTwoFactorChallenge(['code' => '00000'.$i]);
        }

        $this->assertNull($this->attemptTwoFactorChallenge(['code' => '424242']));
        $this->assertTrue(Auth::check());

        $this->startTwoFactorChallenge();

        $this->assertStringContainsString('code was invalid', $this->attemptTwoFactorChallenge(['code' => '000000']));
    }
}
