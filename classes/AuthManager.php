<?php namespace RainLab\User\Classes;

use Redirect;
use October\Rain\Auth\Manager as RainAuthManager;
use RainLab\User\Models\Settings as UserSettings;
use RainLab\User\Models\UserGroup as UserGroupModel;

class AuthManager extends RainAuthManager
{
    protected static $instance;

    protected $sessionKey = 'user_auth';

    protected $userModel = 'RainLab\User\Models\User';

    protected $groupModel = 'RainLab\User\Models\UserGroup';

    protected $throttleModel = 'RainLab\User\Models\Throttle';

    public function init()
    {
        $this->useThrottle = UserSettings::get('use_throttle', $this->useThrottle);
        $this->requireActivation = UserSettings::get('require_activation', $this->requireActivation);
        parent::init();
    }

    /**
     * {@inheritDoc}
     */
    public function extendUserQuery($query)
    {
        $query->withTrashed();
    }

    /**
     * {@inheritDoc}
     */
    public function register(array $credentials, $activate = false)
    {
        if ($guest = $this->findGuestUserByCredentials($credentials)) {
            return $this->convertGuestToUser($guest, $credentials, $activate);
        }

        return parent::register($credentials, $activate);
    }

    //
    // Guest users
    //

    public function findGuestUserByCredentials(array $credentials)
    {
        if ($email = array_get($credentials, 'email')) {
            return $this->findGuestUser($email);
        }

        return null;
    }

    public function findGuestUser($email)
    {
        $query = $this->createUserModelQuery();

        return $user = $query
            ->where('email', $email)
            ->where('is_guest', 1)
            ->first();
    }

    /**
     * Registers a guest user by giving the required credentials.
     *
     * @param array $credentials
     * @return Models\User
     */
    public function registerGuest(array $credentials)
    {
        $user = $this->findGuestUserByCredentials($credentials);
        $newUser = false;

        if (!$user) {
            $user = $this->createUserModel();
            $newUser = true;
        }

        $user->fill($credentials);
        $user->is_guest = true;
        $user->save();

        // Add user to guest group
        if ($newUser && $group = UserGroupModel::getGuestGroup()) {
            $user->groups()->add($group);
        }

        // Prevents revalidation of the password field
        // on subsequent saves to this model object
        $user->password = null;

        return $this->user = $user;
    }

    /**
     * Converts a guest user to a registered user.
     *
     * @param Models\User $user
     * @param array $credentials
     * @param bool $activate
     * @return Models\User
     */
    public function convertGuestToUser($user, $credentials, $activate = false)
    {
        $user->fill($credentials);
        $user->convertToRegistered(false);

        // Remove user from guest group
        if ($group = UserGroupModel::getGuestGroup()) {
            $user->groups()->remove($group);
        }

        if ($activate) {
            $user->attemptActivation($user->getActivationCode());
        }

        // Prevents revalidation of the password field
        // on subsequent saves to this model object
        $user->password = null;

        return $this->user = $user;
    }

    // replaces Redirector::guest to use a seperate session variable in place of url.intended (used on the backend)
    public function guest($path, $status = 302, $headers = [], $secure = null)
    {
        \Session::put('url.frontend.intended', Redirect::getUrlGenerator()->full());
        \Log::info(print_r(\Session::all(), true));

        return Redirect::to($path, $status, $headers, $secure);
    }

    // replaces Redirector::intended to use a seperate session variable in place of url.intended (used on the backend)
    public function intended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        $path = \Session::get('url.frontend.intended', $default);

        return Redirect::to($path, $status, $headers, $secure);
    }

}
