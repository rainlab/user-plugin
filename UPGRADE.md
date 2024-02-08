# Upgrading from User v2 to v3

This guide can be used to help migrate from RainLab.User v2 to v3. Some theme changes are required to since there are new components.

## Upgrade Instructions

1. Run `php artisan plugin:install rainlab.user` to request the latest version (you do not need to uninstall v2 first).

1. Migrate user data using `php artisan user:migratev1` (non destructive).

1. Continue using this plugin as normal.

## Key Differences

- The plugin now uses Laravel's authentication system as the underlying technology.

- Two-Factor authentication is provided out of the box.

## Key Similarities

- ...

## Breaking Changes

### ...

...

### Events Updated

...
