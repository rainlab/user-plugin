# Upgrading from User v2 to v3

This guide can be used to help migrate from RainLab.User v1-v2 to v3. Some theme changes are required to since there are new components.

**Please make sure you have a database and website backup before performing the upgrade.**

## Upgrade Instructions

1. **Make sure you are running October CMS v3.6 or greater.**

1. Run `php artisan plugin:install rainlab.user` to request the latest version (you do not need to uninstall v2 first).

1. Continue using this plugin as normal.

1. Clean up unused table data using `php artisan user:migratev1` (optional).

## Previewing Changes

We recommend installing the `RainLab.Vanilla` theme to demonstrate the latest functionality.

- https://github.com/rainlab/vanilla-theme

## Compatible Plugins

If you are using the following plugins, please upgrade them to v2.0 at the same time as this plugin:

Package | New Version
------- | -----------
`rainlab/forum-plugin` | ^2.0
`rainlab/location-plugin` | ^2.0
`rainlab/userplus-plugin` | ^2.0
`rainlab/notify-plugin` | Optional

If you are using the `rainlab/notify-plugin` for sending the new user notifications, you may uninstall this plugin since these notifications are now included in the user settings.

## Key Differences

- Laravel's authentication system is used as the underlying technology.

- Two-Factor authentication is provided out of the box.

- A User Log has been implemented to track and audit user actions, such as changing email address.

- An Address Book component has been added to the UserPlus plugin.

- The Forum plugin has the ability to "Poke" users to demonstrate the `notifications` component.

## Breaking Changes

### Attributes & Methods

The `RainLab\User\Models\User` model has these attribute changes:

Old Attribute | New Attribute
------------- | -------------
`name`        | `first_name`
`surname`     | `last_name`

The `Auth` facade has these method changes:

Old Method  | New Method
----------- | -------------
`getUser()` | `user()`

### Renamed Settings Model

The `RainLab\User\Models\Settings` model has been renamed to `RainLab\User\Models\Setting` for consistency with the core system design.

### Renamed JWT Auth Method

The `Auth::checkBearerToken` has been renamed to `Auth::loginUsingBearerToken` to be more descriptive of its outcome. The `Auth::checkBearerToken` method still exists and will return the user or `false`, instead of logging in the user internally.

### Account Component Split Up

The functionality of the `Account` component has been split in to three components:

Component | Purpose
--------- | ---------
`Account` | Update user details, enable two factor, delete sessions, delete the user account
`Authentication` | Authenticate a user
`Registration` | Register a new user

### Session Component

The `allowedUserGroups` property has been removed from the `Session` component. This can be supplemented by some Twig code:

```twig
{% if not user.inGroup('premium-users') %}
    {% do redirect('account/login') %}
{% endif %}
```

### Require Activation is Removed

The "Require Activation" functionality has been removed from the settings page for simplicity. The replacement approach below is more flexible, since custom pages or banners can be used based on the requirements.

A user can be checked if they are verified using Twig:

```twig
{% if not user.hasVerifiedEmail %}
    {% do redirect('account/login') %}
{% endif %}
```

Checking administrator approval is possible by creating an "Approved User" user group and placing approved users in that group.

Alternatively, by extending the users database table with a `is_approved` field. Then check in Twig:

```twig
{% if not user.is_approved %}
    {% do redirect('account/login') %}
{% endif %}
```

### MailBlocker model replaced by UserPreference

The `RainLab\User\Models\MailBlocker` model has been replaced by `RainLab\User\Models\UserPreference`. The classes function differently and `UserPreference` is more generic for controlling user preferences.

### Events Updated

The following event names have changed:

- New events added to [the events documentation](./docs/events.md).
- Removed **rainlab.user.reactivate** event, since users can no longer reactivate their account without help from an administrator.

### Feedback

If there are any changes you would like us to include to make upgrading easier, let us know and we can accommodate them in a new release.

Thanks for reading.
