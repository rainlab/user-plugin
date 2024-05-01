# Auth Impersonation

The `Auth` facade supports user impersonation. Use the `impersonate` method to impersonate another user.

```php
Auth::impersonate($user);
```

To stop impersonating, use the `stopImpersonate` method. This will restore the account that was previously logged in, if applicable.

```php
Auth::stopImpersonate();
```

The `isImpersonator` method can be used to check if the user is currently impersonating.

```php
if (Auth::isImpersonator()) {
    // User is currently impersonating another user
}
```

Use the `getRealUser` method to return the underlying user they are impersonating someone else, or it will return the active user if they are not impersonating.

```php
$user = Auth::getRealUser();
```
