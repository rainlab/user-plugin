# Reset Password Component

The reset password component sets a new password for a user. It is used in three cases.

- A user who forgot their password follows the link in a reset email.
- A user invited to the site sets their first password, for example when an administrator creates them with an invitation.
- A signed in user changes their password.

```ini
title = "Reset Password"
url = "/reset-password"

[resetPassword]
isDefault = 1
==
{% component 'resetPassword' %}
```

The form that asks for an email address and sends the reset email belongs to the [Authentication component](./component-authentication.md). This component handles the link in that email.

## Reset Links

Reset and invitation emails link to the page that uses this component, adding the token and email address to the URL as `?reset=...&email=...`. The page is found as follows.

- The component must be declared on the page itself. A component attached to a layout, or to a partial, is not found.
- When more than one page uses the component, enable the `isDefault` property on the reset page. For example, an account page that uses the component to change passwords could otherwise receive the link.
- When no page uses the component, the email contains only `?reset=...&email=...` without a page address.

Reset links expire after 60 minutes, set by the `auth.passwords.users.expire` config value.

Completing a reset also marks the email address as verified, since the user has shown they receive email at that address. Users who cannot sign in because **Require Activation** is enabled can reset their password to confirm their address.

### Headless Integrations

When the reset screen is in a separate application instead of a CMS page, pass the `resetUrl` option to the `recoverPassword` method of the [Action Manager](./action-manager.md).

The URL is set in code, not posted by the form, because anyone submitting a form can change its fields. Otherwise an attacker could request a reset for another user with a link to their own site, then collect the token when that user clicks it.

## API

These AJAX handlers are available.

Handler | Description
------- | -------------
**onResetPassword** | Sets a new password using the token from a reset email
**onConfirmPassword** | Sets the first password using the token from an invitation email
**onChangePassword** | Changes the password of the signed in user

The `onResetPassword` and `onConfirmPassword` handlers expect the `token` and `email` values from the link, along with `password` and an optional `password_confirmation`. The `onChangePassword` handler expects `current_password` instead of the token and email.

These variables are available on the component object.

Variable | Description
-------- | -------------
`hasToken` | returns true if the URL contains a reset token and email address
`hasInvite` | returns true if the link came from an invitation email
`token` | returns the reset token from the URL
`email` | returns the email address from the URL
`user` | returns the logged in user
`canReset` | returns true if there is a reset token or a logged in user

## Examples

The following example sets a new password from a reset link, passing the token and email address from the URL as hidden fields.

```twig
{% if resetPassword.hasToken %}
    <form data-request="onResetPassword" data-request-flash>
        <input type="hidden" name="token" value="{{ resetPassword.token }}" />
        <input type="hidden" name="email" value="{{ resetPassword.email }}" />
        <input type="hidden" name="redirect" value="{{ 'login'|page }}" />

        <input name="password" type="password" placeholder="New password" autocomplete="new-password" />
        <input name="password_confirmation" type="password" placeholder="Confirm password" autocomplete="new-password" />

        <button type="submit">Reset password</button>
    </form>
{% endif %}
```
