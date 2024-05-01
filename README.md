# Front-end user plugin

Front-end user management for October CMS. View this plugin on the October CMS marketplace:

- https://octobercms.com/plugin/rainlab-user

## Requirements

- October CMS 3.6 or above
- The [AJAX Framework](https://docs.octobercms.com/3.x/cms/ajax/introduction.html) to be included in your layout/page

## Installation Instructions

Run the following to install this plugin:

```bash
php artisan plugin:install RainLab.User
```

To uninstall this plugin:

```bash
php artisan plugin:remove RainLab.User
```

### Older Versions

If you are using **October CMS v3.0 - v3.5**, install v2.1 with the following commands:

```bash
composer require rainlab/user-plugin "^2.1"
```

If you are using **October CMS v1.0 - v2.3**, install v1.7 with the following commands:

```bash
composer require rainlab/user-plugin "^1.7"
```

### Sample Theme

We recommend installing this plugin with the `RainLab.Vanilla` theme to demonstrate its functionality.

- https://github.com/rainlab/vanilla-theme

For extra functionality, consider also installing the `RainLab.UserPlus` plugin.

- https://octobercms.com/plugin/rainlab-userplus

## Managing Users

Users are managed on the Users tab found in the admin panel. Each user provides minimal data fields - **First Name**, **Last Name**, **Email** and **Password**. The Name can represent either the person's first name or their full name, making the Last Name field optional, depending on the complexity of your site.

Below the **Email** field is an checkbox to block all outgoing mail sent to the user. This is a useful feature for accounts with an email address that is bouncing mail or has reported spam. When checked, no mail will ever be sent to this address, except for the mail template used for resetting the password.

## Plugin Settings

This plugin creates a Settings menu item, found by navigating to **Settings > Users > User Settings**. This page allows the setting of common features, described in more detail below.

#### Sign In

By default a User will sign in to the site using their email address as a unique identifier. You may use a unique login name instead by changing the **Login Attribute** value to Username. This will introduce a new field called **Username** for each user, allowing them to specify their own short name or alias for identification. Both the Email address and Username must be unique to the user.

As a security precaution, you may restrict users from having sessions across multiple devices at the same time. Enable the **Prevent Concurrent Sessions** setting to use this feature. When a user signs in to their account, it will automatically sign out the user for all other sessions.

#### Registration

Registration to the site is allowed by default. If you are running a closed site, or need to temporarily disable registration, you may disable this feature by switching **Allow user registration** to the OFF setting.

#### Notifications

When a user is first activated, they can be sent a welcome email. To activate the welcome email, select "Notify User" and an email template from the **User Message Template** dropdown. The default message template used is `user:welcome_email` and you can customize this by selecting **Mail > Mail Templates** from the settings menu.

The same applies for notifying the system administrators when a new user joins, with the "Notify Administrators" checkbox. The administrators to notify are selected using **Notify Admin Group** dropdown.

## Documentation

### Getting Started

- [Introduction](./docs/introduction.md)
- [Events](./docs/events.md)

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
