# Front-end user plugin

Front-end user management for October CMS.

## Requirements

- October CMS 3.0 or above
- The [AJAX Framework](https://octobercms.com/docs/cms/ajax) to be included in your layout/page

## Installation Instructions

Run the following to install this plugin:

```bash
php artisan plugin:install RainLab.User
```

To uninstall this plugin:

```bash
php artisan plugin:remove RainLab.User
```

If you are using October CMS v1 or v2, install v1.7 with the following commands:

```bash
composer require rainlab/user-plugin "^1.7"
```

## Managing Users

Users are managed on the Users tab found in the back-end. Each user provides minimal data fields - **First Name**, **Last Name**, **Email** and **Password**. The Name can represent either the person's first name or their full name, making the Last Name field optional, depending on the complexity of your site.

Below the **Email** field is an checkbox to block all outgoing mail sent to the user. This is a useful feature for accounts with an email address that is bouncing mail or has reported spam. When checked, no mail will ever be sent to this address, except for the mail template used for resetting the password.

## Plugin Settings

This plugin creates a Settings menu item, found by navigating to **Settings > Users > User Settings**. This page allows the setting of common features, described in more detail below.

#### Registration

Registration to the site is allowed by default. If you are running a closed site, or need to temporarily disable registration, you may disable this feature by switching **Allow user registration** to the OFF setting.

#### Activation

Activation is a process of vetting a user who joins the site. By default, users are activated automatically when they register and an activated account is required to sign in.

The **Activation mode** specifies the activation workflow:

- **Automatic**: This mode will automatically activate a user when they first register. This is the same as disabling activation entirely and is the default setting.
- **User**: The user can activate their account by responding to a confirmation message sent to their nominated email address.
- **Administrator**: The user can only be activated by an administrator via the back-end area.

You can allow users to sign in without activating by switching **Sign in requires activation** to the OFF setting. This is useful for minimising friction when registering, however with this approach it is often a good idea to disable any "identity sensitive" features until the user has been activated, such as posting content. Alternatively, you could implement a grace period that deletes users (with sufficient warning!) who have not activated within a given period of time.

Users have the ability to resend the activation email by clicking **Send the verification email again** found in the Account component.

#### Sign In

By default a User will sign in to the site using their email address as a unique identifier. You may use a unique login name instead by changing the **Login attribute** value to Username. This will introduce a new field called **Username** for each user, allowing them to specify their own short name or alias for identification. Both the Email address and Username must be unique to the user.

If a user experiences too many failed sign in attempts, their account will be temporarily suspended for a period of time. This feature is enabled by default and will suspend an account for 15 minutes after 5 failed sign in attempts, for a given IP address. You may disable this feature by switching **Throttle attempts** to the OFF setting.

As a security precaution, you may restrict users from having sessions across multiple devices at the same time. Enable the **Prevent concurrent sessions** setting to use this feature. When a user signs in to their account, it will automatically sign out the user for all other sessions.

#### Notifications

This feature is implemented by the Notify plugin. How to use this feature:

- Install the **RainLab.Notify** plugin
- Navigate to **Settings > Notification** rules
- Click **New notification rule**
- Select **User > Activated**
- Click **Add action**
- Select **Compose a mail message**
- Select **User email address** for the **Send to** field
- Here you may select the Mail template previously defined in the user settings.
- Click **Save**

## Extended Features

For extra functionality, consider also installing the [User Plus+ plugin](http://octobercms.com/plugin/rainlab-userplus) (`RainLab.UserPlus`).

## Documentation

### Getting Started

- [Introduction](./introduction.md)
- [Events](./events.md)

### Components

- [Session](./docs/component-session.md)
- [Account](./docs/component-account.md)
- [Authentication](./docs/component-authentication.md)
- [Registration](./docs/component-registration.md)
- [Reset Password](./docs/component-reset-password.md)

### Services

- [Auth Manager](./docs/auth-manager.md)
- [Impersonation](./docs/auth-impersonation.md)
- [Bearer Tokens](./docs/auth-bearer-tokens.md)

### License

This plugin is an official extension of the October CMS platform and is free to use if you have a platform license. See [EULA license](LICENSE.md) for more details.
