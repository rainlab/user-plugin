# Auth Bearer Tokens

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
