# Reset Password Component

The reset password component allows a user to reset their password if they have forgotten it.

```ini
title = "Forgotten your password?"
url = "/forgot-password/:code?"

[resetPassword]
paramCode = "code"
==
{% component 'resetPassword' %}
```

This will display the initial restoration request form and also the password reset form used after the verification email has been received by the user. The `paramCode` is the URL routing code used for resetting the password.
