# Account Component

The account component provides a method to update the logged in user profile, verify email address, enable two-factor authentication, clear browser sessions and delete their account.

```ini
title = "Account"
url = "/account/:code?"

[account]
isDefault = 1
==
...
```

For displaying and clearing other browser sessions for the user, the session driver must be set to `database`. Open the **config/session.php** file and change the driver, this can also be set in the **.env** file with the `SESSION_DRIVER` variable.

```php
'driver' => env('SESSION_DRIVER', 'database'),
```

## API

These AJAX handlers are available.

Handler | Description
------- | -------------
**onUpdateProfile** | Updates the user profile
**onVerifyEmail** | Verifies the user email address
**onEnableTwoFactor** | Enables two-factor authentication
**onConfirmTwoFactor** | Confirms two-factor authentication using a valid code
**onShowTwoFactorRecoveryCodes** | Displays the two-factor recovery codes
**onRegenerateTwoFactorRecoveryCodes** | Deletes and recreates the recovery codes
**onDisableTwoFactor** | Disables two-factor authentication
**onDeleteOtherSessions** | Logs out other user sessions
**onDeleteUser** | Deletes the user account

These variables are available on the component object.

Variable | Description
-------- | -------------
`user` | returns the logged in user
`sessions` | returns browser sessions for the user
`twoFactorEnabled` | returns true if the user has two factor enabled
`twoFactorRecoveryCodes` | returns an array of recovery codes, if available

## Examples

The following example shows how to update the user profile using the `onUpdateProfile` handler.

```html
<form
    method="post"
    class="account-editor"
    data-request="onUpdateProfile"
    data-request-flash>

    <input
        name="first_name"
        type="text"
        value="{{ user.first_name }}"
        class="form-control"
        placeholder="First name"
    />

    <input
        name="last_name"
        type="text"
        value="{{ user.last_name }}"
        class="form-control"
        placeholder="Last name"
    />

    <button
        class="btn btn-primary"
        data-attach-loading
        type="submit">
        Save
    </button>
</form>
```
