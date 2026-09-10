# Session Component

The session component should be added to a layout that has registered users. It has no default markup.

## User Variable

You can check the logged in user by accessing the **{{ user }}** Twig variable:

```twig
{% if user %}
    <p>Hello {{ user.first_name }}</p>
{% else %}
    <p>Nobody is logged in</p>
{% endif %}
```

## Signing Out

The Session component allows a user to sign out of their session.

```html
<a data-request="onLogout" data-request-data="{ redirect: '/good-bye' }">Sign out</a>
```

## Page Restriction

The Session component allows the restriction of a page or layout by allowing only signed in users, only guests or no restriction. This example shows how to restrict a page to users only:

```ini
title = "Restricted page"
url = "/users-only"

[session]
security = "user"
redirect = "home"
```

The `security` property can be user, guest or all. The `redirect` property refers to a page name to redirect to when access is restricted.

## Activation Restriction

The `requireActivation` property restricts access to users who have verified their email address. It is disabled by default. When enabled, a signed in user who has not verified their email is sent to the `redirect` page.

```ini
title = "Members page"
url = "/members-only"

[session]
security = "user"
redirect = "home"
requireActivation = 1
```

Guests are not checked by this property, use the `security` property to restrict guests.

## Group Restriction

The `allowUserGroups` property restricts access to users belonging to the specified group codes, including their primary group. Leave it empty to allow all groups. The optional `redirectGroup` property is used when a signed in user is not in an allowed group, falling back to the `redirect` property when unset.

```ini
title = "Premium page"
url = "/premium-only"

[session]
security = "user"
redirect = "home"
allowUserGroups[] = "premium"
redirectGroup = "upgrade"
```

Guests are not checked by this property, use the `security` property to restrict guests.

## Route Restriction

Access to routes can be restricted by applying the `AuthMiddleware`.

```php
Route::group(['middleware' => \RainLab\User\Classes\AuthMiddleware::class], function () {
    // All routes here will require authentication
});
```

## Token Variable

The `token` Twig variable can be used for generating a new bearer token for the signed in user.

```twig
{% do response(
    ajaxHandler('onLogin').withVars({
        token: session.token
    })
) %}
```

The `checkToken` property of the component is used to verify a supplied token in the request headers `(Authorization: Bearer TOKEN)`.

```ini
[session]
checkToken = 1
```
