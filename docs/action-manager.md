# Action Manager

The `RainLab\User\Classes\ActionManager` class implements the user workflows shared by the CMS components, such as registration, authentication and password recovery. The same workflows can be called directly, making them available to headless integrations such as REST or GraphQL endpoints, without duplicating any logic.

Access the manager using the `instance` method.

```php
$actions = \RainLab\User\Classes\ActionManager::instance();
```

Every workflow enforces the same policies as the CMS components, including registration availability, login throttling, banned user checks and the activation settings.

## Available Actions

Method | Description
------------- | -------------
`registerUser($input, $options)` | creates a new user, returns the user or a custom event response.
`login($input, $options)` | authenticates a user from credentials.
`loginWithTwoFactor($input, $options)` | authenticates and defers to a two factor challenge when set up.
`twoFactorChallenge($input, $options)` | completes a login using a two factor or recovery code.
`logout()` | signs out the user, or reverts an impersonation.
`recoverPassword($input, $options)` | sends a password reset link by email.
`resetPassword($input)` | sets a new password using a reset token.
`changePassword($input)` | updates the password of the signed in user.
`updateProfile($input, $options)` | updates the profile of the signed in user.
`sendVerifyEmail($options)` | sends an email verification link to the signed in user.
`confirmVerifiedEmail($code)` | marks an email address as verified using an emailed code.
`deleteUser($input)` | removes the signed in user, requires their password.
`enableTwoFactor()` | generates a two factor secret, pending confirmation.
`confirmTwoFactor($input)` | verifies a two factor code to complete the set up.
`disableTwoFactor()` | disables two factor authentication.
`regenerateTwoFactorRecoveryCodes()` | issues a new set of recovery codes.
`getTwoFactorRecoveryCodes()` | returns the recovery codes.
`hasTwoFactorEnabled()` | returns true when two factor is set up.
`getBrowserSessions()` | returns the browser sessions, requires the database session driver.
`deleteOtherSessions($input)` | logs out other browser sessions, requires the user password.

Workflows throw a `ValidationException` when the input is invalid, making them compatible with standard AJAX and API error responses.

## Registering a User

The `registerUser` method creates a new user from an input array and signs them in, unless an activation policy defers the sign in.

```php
$user = $actions->registerUser([
    'first_name' => 'Some',
    'email' => 'some@website.tld',
    'password' => 'ChangeMe888',
]);
```

Pass a `verifyUrl` option to override the CMS entry point in the verification email, useful when the confirmation page lives in an external application.

```php
$user = $actions->registerUser($input, [
    'verifyUrl' => 'https://my.app.tld/verify-email'
]);
```

The `canSignInAfterRegister` method checks if the activation policy allows the user to sign in, for example, to inform the response that the account is awaiting approval.

```php
if (!$actions->canSignInAfterRegister($user)) {
    // Inform the user to check their email or wait for approval
}
```

## Authentication

The `login` method authenticates the user from credentials and starts a session. The `remember` option persists the session with a cookie.

```php
$actions->login([
    'email' => 'some@website.tld',
    'password' => 'ChangeMe888',
], [
    'remember' => true
]);
```

Use `loginWithTwoFactor` to respect two factor authentication, it returns the `ActionManager::TWO_FACTOR_CHALLENGE` constant when the user must complete a challenge, submitted with the `twoFactorChallenge` method.

```php
$result = $actions->loginWithTwoFactor($input);

if ($result === \RainLab\User\Classes\ActionManager::TWO_FACTOR_CHALLENGE) {
    // Ask for the two factor code, then complete the login
    $actions->twoFactorChallenge(['code' => '123456']);
}
```

## Password Recovery

The `recoverPassword` method sends a reset link to the supplied email address. Pass a `resetUrl` option to override the CMS entry point in the email, the reset token and email address are appended as query parameters.

```php
$actions->recoverPassword([
    'email' => 'some@website.tld'
], [
    'resetUrl' => 'https://my.app.tld/reset-password'
]);
```

The `resetPassword` method completes the process using the token from the email.

```php
$actions->resetPassword([
    'email' => 'some@website.tld',
    'token' => $token,
    'password' => 'NewPassword888',
]);
```

## Events

All events fired by the workflows, such as `rainlab.user.beforeRegister` and `rainlab.user.login`, continue to fire when called directly. When a workflow runs inside a CMS component, the component is passed as the first event argument, otherwise the manager instance takes its place.

The `withContext` method returns a copy of the manager that fires events through a host object, this is how the CMS components invoke their workflows.

```php
$actions = \RainLab\User\Classes\ActionManager::instance()->withContext($this);
```
