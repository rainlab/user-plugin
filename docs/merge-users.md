# Merge Users

When users check out as guests, the system creates a new guest user record each time. This can result in multiple user records for the same person. The merge feature lets an administrator consolidate these duplicate users into a single account.

## How It Works

Merging transfers all owned records (orders, invoices, etc.) from one or more users into a single **leading user**. The leading user keeps all of its own attributes (name, email, groups, etc.) and absorbs the relational records from the merged users. The merged users are then permanently deleted.

### What Gets Merged

- **Relational records** with a `user_id` foreign key are reassigned to the leading user (e.g. orders, invoices, credit notes)
- **Activity log** entries are reassigned so the leading user retains a complete history

### What Does Not Get Merged

- **User attributes** like name, email, and password are kept from the leading user only
- **Groups** (primary and secondary) are not combined — the leading user keeps its own group memberships

## Backend Usage

To merge users from the admin panel:

1. Navigate to the **Users** list
2. Select two or more users using the checkboxes
3. Click **More Actions** → **Merge Users**
4. A popup will display the selected users — choose which one should be the **leading user**
5. Click **Merge & Delete Users** to confirm

The non-leading users will be permanently deleted and all of their records will be transferred to the leading user. An activity log entry is recorded for each merge.

## Programmatic Usage

You can merge users in code using the `mergeUser` method on the User model.

```php
use RainLab\User\Models\User;

$leadingUser = User::find(1);
$duplicateUser = User::find(2);

$leadingUser->mergeUser($duplicateUser);
```

This will fire the `rainlab.user.mergeUser` event, reassign core relations, and permanently delete the duplicate user.

## Extending with Events

When users are merged, the `rainlab.user.mergeUser` event is fired. Plugins that store records with a `user_id` foreign key should listen for this event and reassign their records.

```php
Event::listen('rainlab.user.mergeUser', function($leadingUser, $mergedUser) {
    \Acme\Blog\Models\Post::where('user_id', $mergedUser->id)
        ->update(['user_id' => $leadingUser->id]);

    \Acme\Blog\Models\Comment::where('user_id', $mergedUser->id)
        ->update(['user_id' => $leadingUser->id]);
});
```

The event receives two arguments:

Argument | Description
-------- | -----------
**$leadingUser** | The user that will retain all records (the surviving account).
**$mergedUser** | The user being absorbed. This user will be permanently deleted after the event fires.