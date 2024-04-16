# Upgrading from User v2 to v3

This guide can be used to help migrate from RainLab.User v1-v2 to v3. Some theme changes are required to since there are new components.

## Upgrade Instructions

1. **Make sure you are running October CMS v3.6 or greater.**

1. Run `php artisan plugin:install rainlab.user` to request the latest version (you do not need to uninstall v2 first).

1. Continue using this plugin as normal.

1. Clean up unused table data using `php artisan user:migratev1` (optional).

## Key Differences

- The plugin now uses Laravel's authentication system as the underlying technology.

- Two-Factor authentication is provided out of the box.

- A User Log has been implemented to track and audit user actions, such as changing email address.

## Breaking Changes

### Renamed Settings Model

The `RainLab\User\Models\Settings` model has been renamed to `RainLab\User\Models\Setting` for consistency with the core system design.,

### Renamed JWT Auth Method

The `Auth::checkBearerToken` has been renamed to `Auth::loginUsingBearerToken` to be more descriptive of its outcome.

### Account Component Split Up

The functionality of the Account component has been broken up in to three components

- Account: update user details, enable two factor, delete sessions, delete the user account
- Authentication: authenticate a user
- Registration: register a new user

### Session Component

The `allowedUserGroups` property has been removed from the `Session` component. This can be supplemented by some Twig code:

```twig
{% if not user.inGroup('premium-users') %}
    {% do redirect('account/login') %}
{% endif }
```

### MailBlocker model replaced by UserPreference

The `RainLab\User\Models\MailBlocker` model has been replaced by `RainLab\User\Models\UserPreference`. The classes function differently and `UserPreference` is more generic for controlling user preferences.

### Require Activation is Removed

The Require Activation functionality has been removed for simplicity. This approach is more flexible, since custom pages or banners can be used based on the requirements.

A user can be checked if they are verified using Twig:

```twig
{% if not user.hasVerifiedEmail %}
    {% do redirect('account/login') %}
{% endif %}
```

It is possible to require administration approval by creating an "Approved User" user group, or by extending the users database table with a `is_approved` field. Then check in Twig:

```twig
{% if not user.is_approved %}
    {% do redirect('account/login') %}
{% endif %}
```

### Events Updated

The following event names have changed:

- Removed **rainlab.user.reactivate** event, since users can no longer reactivate their account without help from an administrator.
