# Tailor Integration

This plugin includes integration with Tailor by providing content fields.

## Users Field

The `users` field type allows association to one or more users via a Tailor Blueprint. The functionality is introduced by the `RainLab\User\ContentFields\UsersField` PHP class.

The simplest example is to associate to a single user (belongs to relationship).

```yaml
users:
    label: Users
    type: users
    maxItems: 1
```

Set the `maxItems` to **0** to associate to an unlimited number of users (belongs to many relationship).

```yaml
users:
    label: Users
    type: users
    maxItems: 0
```

Set the `displayMode` to **controller** to show a more advanced user interface for user management.

```yaml
users:
    label: Users
    type: users
    maxItems: 0
    displayMode: controller
```

You may also set the `displayMode` to **taglist** to select users by their email address.

```yaml
users:
    label: Users
    type: users
    maxItems: 0
    displayMode: taglist
```
