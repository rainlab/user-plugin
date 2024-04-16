# Events

This plugin will fire some global events that can be useful for interacting with other plugins.

- **rainlab.user.authenticateUser**: Provides custom logic for logging in a user during authentication.
- **rainlab.user.authenticationResponse**: Provides custom response logic after authentication.
- **rainlab.user.authenticationLockout**: Provides custom logic when a login attempt has been rate limited.
- **rainlab.user.registerNewUser**: Provides custom logic for creating a new user during registration.
- **rainlab.user.registrationResponse**: Provides custom response logic after registration.
- **rainlab.user.logoutResponse**: Provides custom response logic for logging out a user.
- **rainlab.user.passwordReset**: Provides custom logic for resetting a user password.
- **rainlab.user.canDeleteUser**: Triggered before a user is deleted. This event should return true if the user has dependencies and should be soft deleted to retain those relationships and allow the user to be restored. Otherwise, it will be deleted forever.

- **rainlab.user.beforeRegister**: Before the user's registration is processed. Passed the `$data` variable by reference to enable direct modifications to the `$data` provided to the `Auth::register()` method.
- **rainlab.user.register**: The user has successfully registered. Passed the `$user` object and the submitted `$data` variable.
- **rainlab.user.beforeAuthenticate**: Before the user is attempting to authenticate using the Account component.
- **rainlab.user.login**: The user has successfully signed in.
- **rainlab.user.logout**: The user has successfully signed out.
- **rainlab.user.activate**: The user has verified their email address.
- **rainlab.user.deactivate**: The user has opted-out of the site by deactivating their account. This should be used to disable any content the user may want removed.
- **rainlab.user.getNotificationVars**: Fires when sending a user notification to enable passing more variables to the email templates. Passes the `$user` model the template will be for.
- **rainlab.user.view.extendListToolbar**: Fires when the user listing page's toolbar is rendered.
- **rainlab.user.view.extendPreviewToolbar**: Fires when the user preview page's toolbar is rendered.
- **rainlab.user.view.extendPreviewTabs**: Provides an opportunity to add tabs to the user preview page in the admin panel. The event should return an array of `[Tab Name => ~/path/to/partial.php]`

Here is an example of hooking an event:

```php
Event::listen('rainlab.user.deactivate', function($user) {
    // Hide all posts by the user
});
```

A common requirement is to adapt another to a legacy authentication system. In the example below, the `WordPressLogin::check` method would check the user password using an alternative hashing method, and if successful, update to the new one used by October.

```php
Event::listen('rainlab.user.beforeAuthenticate', function($component, $credentials) {
    $login = array_get($credentials, 'login');
    $password = array_get($credentials, 'password');

    // No such user exists
    if (!$user = Auth::retrieveByCredentials(['login' => $login])) {
        return;
    }

    // The user is logging in with their old WordPress account
    // for the first time. Rehash their password using the new
    // October system.
    if (WordPressLogin::check($user->password, $password)) {
        $user->password = $user->password_confirmation = $password;
        $user->forceSave();
    }
});
```
