# Events

This plugin will fire some global events that can be useful for interacting with other plugins.

- **rainlab.user.beforeRegister**: Before the user's registration is processed. Passed the `$data` variable by reference to enable direct modifications to the `$data` provided to the `Auth::register()` method.
- **rainlab.user.register**: The user has successfully registered. Passed the `$user` object and the submitted `$data` variable.
- **rainlab.user.beforeAuthenticate**: Before the user is attempting to authenticate using the Account component.
- **rainlab.user.login**: The user has successfully signed in.
- **rainlab.user.logout**: The user has successfully signed out.
- **rainlab.user.deactivate**: The user has opted-out of the site by deactivating their account. This should be used to disable any content the user may want removed.
- **rainlab.user.reactivate**: The user has reactivated their own account by signing back in. This should revive the users content on the site.
- **rainlab.user.getNotificationVars**: Fires when sending a user notification to enable passing more variables to the email templates. Passes the `$user` model the template will be for.
- **rainlab.user.view.extendListToolbar**: Fires when the user listing page's toolbar is rendered.
- **rainlab.user.view.extendPreviewToolbar**: Fires when the user preview page's toolbar is rendered.

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
    if (!$user = Auth::findUserByLogin($login)) {
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
