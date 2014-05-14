# Front-end user plugin
===========

This plugin adds front-end user management features to [OctoberCMS](http://octobercms.com).

## Session component

The session component should be added to a layout that has registered users. It has no default markup.

### User variable

You can check the logged in user by accessing the **{{ user }}** Twig variable:

    {% if user %}
        <p>Hello {{ user.name }}</p>
    {% else %}
        <p>Nobody is logged in</p>
    {% endif %}

### Signing out

The Session component allows a user to sign out of their session.

    <a data-request="onLogout" data-request-data="redirect: '/good-bye'">Sign out</a>

### Page restriction

The Session component allows the restriction of a page or layout by allowing only signed in users, only guests or no restriction. This example shows how to restrict a page to users only:

    title = "Restricted page"
    url = "/users-only"

    [session]
    security = "user"
    redirect = "home"

The `security` property can be user, guest or all. The `redirect` property refers to a page name to redirect to when access is restricted.

## Account component

The account component provides a user sign in form, registration form, activation form and update form.

