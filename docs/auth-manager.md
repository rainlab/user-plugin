# Auth Manager

There is an `Auth` facade you may use for common tasks, it primarily inherits the `October\Rain\Auth\Manager` class for functionality.

You may use `create` method on the `User` model to register an account.

```php
$user = \RainLab\User\Models\User::create([
    'name' => 'Some User',
    'email' => 'some@website.tld',
    'password' => 'changeme',
    'password_confirmation' => 'changeme',
]);
```

The `markEmailAsVerified` method can be used to activate an existing user.

```php
// Auto activate this user
$user->markEmailAsVerified();
```

The `check` method is a quick way to check if the user is signed in.

```php
// Returns true if signed in.
$loggedIn = Auth::check();
```

To return the user model that is signed in, use `user` method instead.

```php
// Returns the signed in user
$user = Auth::user();
```

You may authenticate a user by providing their login and password with the `attempt` method.

```php
// Authenticate user by credentials
$user = Auth::attempt([
    'email' => post('email'),
    'password' => post('password')
]);
```

The second argument is used to store a non-expire cookie for the user.

```php
$user = Auth::attempt([...], true);
```

You can also authenticate as a user simply by passing the user model along with the `login` method.

```php
// Sign in as a specific user
Auth::login($user);
```

The second argument will store the non-expire cookie for the user.

```php
// Sign in and remember the user
Auth::login($user, true);
```

You may look up a user by their login name using the `retrieveByCredentials` method.

```php
$user = Auth::retrieveByCredentials(['email' => 'some@email.tld']);
```

## Guest Users

Creating a guest user allows the registration process to be deferred. For example, making a purchase without needing to register first. Guest users are not able to sign in and will be added to the user group with the code `guest`.

Use the `is_guest` attribute to create a guest user, it will return a user object and can be called multiple times. The unique identifier is the email address, which is a required field.

```php
$user = \RainLab\User\Models\User::([
    'email' => 'person@acme.tld',
    'is_guest' => true
]);
```

When a user registers with the same email address using the `User::create` method, they will inherit the existing guest user account.

```php
// This will not throw an "Email already taken" error
$user = \RainLab\User\Models\User::create([
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
