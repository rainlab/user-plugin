# Auth Manager

There is an `Auth` facade you may use for common tasks, it primarily inherits the `October\Rain\Auth\Manager` class for functionality.

You may use `register` method to register an account.

```php
$user = Auth::register([
    'name' => 'Some User',
    'email' => 'some@website.tld',
    'password' => 'changeme',
    'password_confirmation' => 'changeme',
]);
```

The second argument can specify if the account should be automatically activated:

```php
// Auto activate this user
$user = Auth::register([...], true);
```

The `Auth::check` method is a quick way to check if the user is signed in.

```php
// Returns true if signed in.
$loggedIn = Auth::check();
```

To return the user model that is signed in, use `Auth::getUser` instead.

```php
// Returns the signed in user
$user = Auth::getUser();
```

You may authenticate a user by providing their login and password with `Auth::authenticate`.

```php
// Authenticate user by credentials
$user = Auth::authenticate([
    'login' => post('login'),
    'password' => post('password')
]);
```

The second argument is used to store a non-expire cookie for the user.

```php
$user = Auth::authenticate([...], true);
```

You can also authenticate as a user simply by passing the user model along with `Auth::login`.

```php
// Sign in as a specific user
Auth::login($user);
```

The second argument is the same.

```php
// Sign in and remember the user
Auth::login($user, true);
```

You may look up a user by their login name using the `Auth::findUserByLogin` method.

```php
$user = Auth::findUserByLogin('some@email.tld');
```

When working with authentication via bearer tokens, the `Auth::getBearerToken` method can be used to obtain a bearer token (JWT) for the current user. It expires after 1 hour by default.

```php
$token = Auth::getBearerToken();
```

The `Auth::checkBearerToken` method is used to verify a supplied token and authenticate the user. The method returns `true` if the verification was successful.

```php
if ($jwtToken = Request::bearerToken()) {
    Auth::checkBearerToken($jwtToken);
}
```

## Guest Users

Creating a guest user allows the registration process to be deferred. For example, making a purchase without needing to register first. Guest users are not able to sign in and will be added to the user group with the code `guest`.

Use the `Auth::registerGuest` method to create a guest user, it will return a user object and can be called multiple times. The unique identifier is the email address, which is a required field.

```php
$user = Auth::registerGuest(['email' => 'person@acme.tld']);
```

When a user registers with the same email address using the `Auth::register` method, they will inherit the existing guest user account.

```php
// This will not throw an "Email already taken" error
$user = Auth::register([
    'email' => 'person@acme.tld',
    'password' => 'changeme',
    'password_confirmation' => 'changeme',
]);
```

> **Important**: If you are using guest accounts, it is important to disable sensitive functionality for user accounts that are not verified, since it may be possible for anyone to inherit a guest account.

You may also convert a guest to a registered user with the `convertToRegistered` method. This will generate a random password and sends an invitation using the `rainlab.user::mail.invite` template.

```php
$user->convertToRegistered();
```

To disable the notification and password reset, pass the first argument as false.

```php
$user->convertToRegistered(false);
```
