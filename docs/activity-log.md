# Activity Log

The User plugin includes an activity log that records key events in a user's lifecycle, such as signing in, changing their password, or being banned by an administrator. These events are displayed in a timeline view accessible from the backend.

## Logging Custom Events

Other plugins can log custom events to a user's activity timeline using the `UserLog` model.

```php
use RainLab\User\Models\UserLog;

UserLog::createRecord($userId, 'acme-order-placed', [
    'order_id' => $order->id,
    'order_total' => $order->total,
]);
```

Use the `createRecord` method for user-initiated events and the `createSystemRecord` method for events initiated by an administrator.

```php
UserLog::createSystemRecord($userId, 'acme-order-refunded', [
    'order_id' => $order->id,
]);
```

Both methods accept the following arguments.

Argument | Description
-------- | -----------
**$userId** | the user ID the log entry belongs to
**$type** | a unique string identifier for the event type
**$data** | an optional array of extra data stored as JSON

The IP address is captured automatically with each log entry.

## Registering a Detail Partial

Each log type renders its detail text using a partial file. Listen to the `rainlab.user.extendLogDetailViewPath` event to return a path to your custom partial.

```php
Event::listen('rainlab.user.extendLogDetailViewPath', function($record, $type) {
    if ($type === 'acme-order-placed') {
        return plugins_path('acme/shop/models/userlog/_detail_order_placed.php');
    }
});
```

Inside the partial, the `$record` variable is the `UserLog` model instance. You can access any data stored in the `$data` array as attributes on the record, along with the following built-in attributes.

Attribute | Description
--------- | -----------
**$record->actor_user_name** | the name of the affected user
**$record->actor_user_name_linked** | the user name as an HTML link to their profile
**$record->actor_admin_name** | the name of the admin who performed the action
**$record->actor_admin_name_linked** | the admin name as an HTML link to their profile
**$record->is_system** | true if the event was initiated by an admin

Here is an example partial.

```php
<?= __(":name placed order #:order_id", [
    'name' => $record->actor_user_name_linked,
    'order_id' => $record->order_id,
]) ?>
```

If no partial is found for a log type, the timeline will display the type string followed by "event" as a fallback.

## Registering Filter Options

Log types registered by your plugin can appear in the activity type filter on the Timelines page. Listen to the `rainlab.user.extendLogTypeOptions` event and return an array of type identifiers and their labels.

```php
Event::listen('rainlab.user.extendLogTypeOptions', function() {
    return [
        'acme-order-placed' => __("Order Placed"),
        'acme-order-refunded' => __("Order Refunded"),
    ];
});
```

## Available Log Types

The following log types are included with the plugin.

Type | Constant | Description
---- | -------- | -----------
new-user | `TYPE_NEW_USER` | a new user was created
set-email | `TYPE_SET_EMAIL` | a user's email address was changed
set-password | `TYPE_SET_PASSWORD` | a user's password was changed by an admin
set-two-factor | `TYPE_SET_TWO_FACTOR` | two-factor authentication was enabled or disabled
self-verify | `TYPE_SELF_VERIFY` | a user verified their email address
self-login | `TYPE_SELF_LOGIN` | a user signed in
self-delete | `TYPE_SELF_DELETE` | a user deleted their account
self-password-reset | `TYPE_SELF_PASSWORD_RESET` | a user reset their password via the forgot password flow
self-password-change | `TYPE_SELF_PASSWORD_CHANGE` | a user changed their password from their account
admin-impersonate | `TYPE_ADMIN_IMPERSONATE` | an admin impersonated a user
admin-ban | `TYPE_ADMIN_BAN` | an admin banned a user
admin-unban | `TYPE_ADMIN_UNBAN` | an admin unbanned a user
admin-delete | `TYPE_ADMIN_DELETE` | an admin deleted a user
admin-restore | `TYPE_ADMIN_RESTORE` | an admin restored a soft-deleted user
admin-convert-guest | `TYPE_ADMIN_CONVERT_GUEST` | an admin converted a guest to a registered user
internal-comment | `TYPE_INTERNAL_COMMENT` | an internal comment added by an admin
