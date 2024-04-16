# Upgrading from User v2 to v3

This guide can be used to help migrate from RainLab.User v2 to v3. Some theme changes are required to since there are new components.

## Upgrade Instructions

1. Run `php artisan plugin:install rainlab.user` to request the latest version (you do not need to uninstall v2 first).

1. Migrate user data using `php artisan user:migratev1` (non destructive).

1. Continue using this plugin as normal.

## Key Differences

- The plugin now uses Laravel's authentication system as the underlying technology.

- Two-Factor authentication is provided out of the box.

- A User Log has been implemented to track and audit user actions, such as changing email address.

## Key Similarities

- ...

## Breaking Changes

### Methods

- User::findByEmail → Auth::retrieveByCredentials

- RainLab\User\Models\Settings → RainLab\User\Models\Setting

- Auth::checkBearerToken → Auth::loginUsingBearerToken

### Account Component Split Up

The functionality of the Account component has been broken up in to three components

- Account: update user details, enable two factor, delete sessions, delete the user account
- Authentication: authenticate a user
- Registration: register a new user

### Session Component

The `allowedUserGroups` property has been renamed to `allowUserGroups`.

### MailBlocker model replaced by UserPreference

The `RainLab\User\Models\MailBlocker` model has been replaced by `RainLab\User\Models\UserPreference`. The classes function differently and `UserPreference` is more generic for controlling user preferences.



### Events Updated

...
