# Account Component

The account component provides a user sign in form, registration form, activation form and update form. To display the form:

```ini
title = "Account"
url = "/account/:code?"

[account]
redirect = "home"
paramCode = "code"
==
{% component 'account' %}
```

If the user is logged out, this will display a sign in and registration form. Otherwise, it will display an update form. The `redirect` property is the page name to redirect to after the submit process is complete. The `paramCode` is the URL routing code used for activating the user, only used if the feature is enabled.
