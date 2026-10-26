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

## Email Verification

Verification emails link to the page that uses this component, adding a code to the URL as `?verify=...`. The component confirms the address when the page loads, then redirects to remove the code. The page is found the same way as for [reset links](./component-reset-password.md), so the component must be declared on the page itself, and the `isDefault` property picks the page when more than one uses the component.

The link expires after 60 minutes, set by the `auth.verification.expire` config value. Logged in users can request a new link with the `onVerifyEmail` handler.

```twig
{% if account.user and not account.user.hasVerifiedEmail %}
    <button data-request="onVerifyEmail" data-request-flash>
        Send the verification email again
    </button>
{% endif %}
```

When **Require Activation** is enabled, unverified users cannot sign in, so they cannot request a new link this way. Resetting their password also verifies the address, see the [Reset Password component](./component-reset-password.md).

For the same reason, the page that handles the link must be open to signed out users when Require Activation is enabled. A [Session component](./component-session.md) on the page, or its layout, that requires a logged in user sends them to sign in before the code is checked. If your account page requires a logged in user, enable `isDefault` on a separate page that does not.

## API

These AJAX handlers are available.

Handler | Description
------- | -------------
**onUpdateProfile** | Updates the user profile
**onVerifyEmail** | Sends a new verification email to the logged in user
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
