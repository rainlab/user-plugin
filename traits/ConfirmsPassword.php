<?php namespace RainLab\User\Traits;

use Auth;
use Config;
use Session;
use RainLab\User\Helpers\User as UserHelper;
use ValidationException;

/**
 * ConfirmsPassword confirms the user password
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait ConfirmsPassword
{
    /**
     * onConfirmPassword confirm the user's password from the browser.
     */
    public function onConfirmPassword()
    {
        if (!$this->isUserPasswordValid(post('confirmable_password'))) {
            throw new ValidationException([
                'confirmable_password' => __('This password does not match our records.'),
            ]);
        }

        Session::put(['auth.password_confirmed_at' => time()]);

        $this->dispatchBrowserEvent('app:password-confirmed');

        return ['passwordConfirmed' => true];
    }

    /**
     * isUserPasswordValid
     */
    protected function isUserPasswordValid(string $password): bool
    {
        $user = Auth::user();
        $username = UserHelper::username();

        if (!$user || !$password) {
            return false;
        }

        return Auth::validate([
            $username => $user->{$username},
            'password' => $password
        ]);
    }

    /**
     * checkConfirmedPassword checks if the user password has been confirmed
     */
    protected function checkConfirmedPassword()
    {
        if ($this->passwordIsConfirmed()) {
            return;
        }

        $this->dispatchBrowserEvent('app:password-confirming');

        return ['confirmingPassword' => true];
    }

    /**
     * passwordIsConfirmed determine if the user's password has been recently confirmed.
     */
    protected function passwordIsConfirmed(int $maxSecondsSinceConfirmation = null): bool
    {
        $maxSecondsSinceConfirmation = $maxSecondsSinceConfirmation ?: Config::get('auth.password_timeout', 900);

        return (time() - Session::get('auth.password_confirmed_at', 0)) < $maxSecondsSinceConfirmation;
    }
}
