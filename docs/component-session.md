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
