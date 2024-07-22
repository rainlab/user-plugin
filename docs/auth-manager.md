# Auth Manager

There is an `Auth` facade you may use for common tasks, it primarily inherits the `October\Rain\Auth\Manager` class for functionality.

You may use `create` method on the `User` model to register an account.

```php
$user = \RainLab\User\Models\User::create([
    'first_name' => 'Some',
    'last_name' => 'User',
    'email' => 'some@website.tld',
    'password' => 'ChangeMe888',
    'password_confirmation' => 'ChangeMe888',
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

You may look up a user by their email or login name using the `retrieveByCredentials` method via the provider class.

```php
$user = Auth::getProvider()->retrieveByCredentials([
    'email' => 'some@email.tld'
]);
```

## Guest Users

Creating a guest user allows the registration process to be deferred. For example, making a purchase without needing to register first. Guest users are not able to sign in and will be added to the user group with the code `guest`.

> **Note**: If you are upgrading from an older version of this plugin, to enable guest users you may need to remove the UNIQUE index on the `email` column in the `users` table.

Use the `is_guest` attribute to create a guest user, it will return a user object and can be called multiple times. The unique identifier is the email address, which is a required field.

```php
$user = \RainLab\User\Models\User::create([
    'first_name' => 'Some',
    'last_name' => 'User',
    'email' => 'person@acme.tld',
    'is_guest' => true
]);
```

When a user registers with the same email address using the `create` method, another account is created and they will not inherit the existing guest user account.

```php
// This will not throw an "Email already taken" error
$user = \RainLab\User\Models\User::create([
    'first_name' => 'Some',
    'last_name' => 'User',
    'email' => 'person@acme.tld',
    'password' => 'ChangeMe888',
    'password_confirmation' => 'ChangeMe888',
]);
```

You may convert a guest to a registered user with the `convertToRegistered` method. This will send them an invitation using the `user:invite_email` template to set up a new password. When a user is converted they will be added to the user group with the code `registered`.

```php
User::where('email', 'person@acme.tld')->first();
$user->convertToRegistered();
```

To disable the notification and password reset, pass the first argument as false.

```php
$user->convertToRegistered(false);
```
