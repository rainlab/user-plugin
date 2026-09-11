<?php

use RainLab\User\Models\User;
use RainLab\User\Models\Setting;
use RainLab\User\Classes\ActionManager;

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
}
