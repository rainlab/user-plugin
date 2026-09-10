<?php namespace RainLab\User\Classes;

use App;
use Auth;
use Event;
use Request;
use RainLab\User\Models\User;
use RainLab\User\Models\UserLog;
use RainLab\User\Helpers\User as UserHelper;
use Illuminate\Contracts\Auth\PasswordBroker;

/**
 * ActionManager implements user workflows shared by CMS components and
 * headless integrations, such as REST or GraphQL endpoints.
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
class ActionManager
{
    use \RainLab\User\Classes\ActionManager\ActionLogin;
    use \RainLab\User\Classes\ActionManager\ActionLogout;
    use \RainLab\User\Classes\ActionManager\ActionRegisterUser;
    use \RainLab\User\Classes\ActionManager\ActionTwoFactorLogin;
    use \RainLab\User\Classes\ActionManager\ActionRecoverPassword;
    use \RainLab\User\Classes\ActionManager\ActionResetPassword;
    use \RainLab\User\Classes\ActionManager\ActionChangePassword;
    use \RainLab\User\Classes\ActionManager\ActionUpdateProfile;
    use \RainLab\User\Classes\ActionManager\ActionVerifyEmail;
    use \RainLab\User\Classes\ActionManager\ActionDeleteUser;
    use \RainLab\User\Classes\ActionManager\ActionTwoFactor;
    use \RainLab\User\Classes\ActionManager\ActionBrowserSessions;

    /**
     * @var string TWO_FACTOR_CHALLENGE result when a login must complete a two factor challenge
     */
    const TWO_FACTOR_CHALLENGE = 'two-factor-challenge';

    /**
     * @var object|null context is an optional host object used to emit events, typically a CMS component
     */
    protected $context;

    /**
     * instance of the action manager
     */
    public static function instance(): static
    {
        return App::make('user.actions');
    }

    /**
     * withContext returns a copy of this manager that fires events through the given host object
     */
    public function withContext($context): static
    {
        $manager = clone $this;
        $manager->context = $context;

        return $manager;
    }

    /**
     * user returns the currently authenticated user
     */
    protected function user(): ?User
    {
        return Auth::user();
    }

    /**
     * isUserPasswordValid checks a supplied password against the current user
     */
    protected function isUserPasswordValid(string $password): bool
    {
        $user = $this->user();
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
     * makePasswordBroker to be used during password reset
     */
    protected function makePasswordBroker(): PasswordBroker
    {
        return App::make('auth.password')->broker('users');
    }

    /**
     * prepareAuthenticatedSession protects against session fixation
     */
    protected function prepareAuthenticatedSession()
    {
        if (Request::hasSession()) {
            Request::session()->regenerate();
        }
    }

    /**
     * recordUserLogAuthenticated
     */
    protected function recordUserLogAuthenticated($user, $twoFactor = false)
    {
        UserLog::createRecord($user->getKey(), UserLog::TYPE_SELF_LOGIN, [
            'user_full_name' => $user->full_name,
            'is_two_factor' => $twoFactor
        ]);
    }

    /**
     * fireSystemEvent fires through the context host when one is set, otherwise the
     * global event fires with this manager as the emitting object
     */
    protected function fireSystemEvent(string $event, array $params = [], bool $halt = true)
    {
        if ($this->context) {
            return $this->context->fireSystemEvent($event, $params, $halt);
        }

        return Event::fire($event, array_merge([$this], $params), $halt);
    }
}
