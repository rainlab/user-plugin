# Auth Bearer Tokens

The `Auth` implements a native bearer token implementation (JWT).

## Generating a Token

When working with authentication via bearer tokens, the `getBearerToken` method can be used to obtain a bearer token (JWT) for the current user. It expires after 1 hour by default.

```php
$token = Auth::getBearerToken();
```

You may also pass a user to this method to get a token for a specified user.

```php
$token = Auth::getBearerToken($user);
```

When using the [Session component](./component-session.md), the `token` variable is available on this object.

```twig
{{ session.key}}
```

## Verifying a Token

When verifying a token, use the `checkBearerToken` method that will return a valid user who is associated token, or false if the token is invalid or that user is no longer found.

```php
$user = Auth::checkBearerToken($token);
```

The `loginUsingBearerToken` method is used to verify a supplied token and authenticate the user. The method returns the user if the verification was successful.

```php
if ($jwtToken = Request::bearerToken()) {
    Auth::loginUsingBearerToken($jwtToken);
}
```

> **Note**: Further configuration for this functionality can be found in the **rainlab.user::config.bearer_token** configuration value.

## Working with APIs

When [building API endpoints using CMS pages](https://docs.octobercms.com/3.x/cms/resources/building-apis.html) it can be useful to use a page for handling the authentication logic. The following is a simple example that includes various API endpoints.

```twig
title = "User API Page"
url = "/api/user/:action"

[resetPassword]
[account]
[session]
checkToken = 1
==
{% if this.param.action == 'signin' %}
    {% do response(
        ajaxHandler('onSignin').withVars({
            token: session.token()
        })
    ) %}
{% endif %}

{% if this.param.action == 'register' %}
    {% do response(ajaxHandler('onRegister')) %}
{% endif %}

{% if this.param.action == 'logout' %}
    {% do response(ajaxHandler('onLogout')) %}
{% endif %}

{% if this.param.action == 'refresh' %}
    {% do response({ data: {
        token: session.token()
    }}) %}
{% endif %}
```

An API layout to verify the user can be used for other API endpoints.

```twig
description = "Auth API Layout"
is_priority = 1

[session]
checkToken = 1
==
{% if session.user %}
    {% page %}
{% else %}
    {% do abort(403, 'Access Denied') %}
{% endif %}
```
