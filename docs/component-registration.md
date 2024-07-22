# Registration Component

## Using a Login Name

By default the User plugin will use the email address as the login name. To switch to using a user defined login name, navigate to the backend under System > Users > User Settings and change the Login attribute under the Sign in tab to be **Username**. Then simply ask for a username upon registration by adding the username field:

```twig
<form data-request="onRegister">
    <label>Full Name</label>
    <input name="name" type="text" placeholder="Enter your full name">

    <label>Email</label>
    <input name="email" type="email" placeholder="Enter your email">

    <label>Username</label>
    <input name="username" placeholder="Pick a login name">

    <label>Password</label>
    <input name="password" type="password" placeholder="Choose a password">

    <button type="submit">Register</button>
</form>
```

We can add any other additional fields here too, such as `phone`, `company`, etc.



## Password Requirements

### Password Length

By default, the User plugin requires a minimum password length of 8 characters for all users when registering or changing their password. You can change this length requirement by going to backend and navigating to System > Users > User Settings. Inside the Registration tab, a **Minimum password length** field is provided, allowing you to increase or decrease this limit to your preferred length.

### Password Confirmation

The `password_confirmation` field can be used to prompt the user to enter their password a second time. This input name is optional, if it is found in the postback data, then it will be validated. The following is an example.

```twig
<input name="password" type="password" placeholder="Choose a password" />
<input name="password_confirmation" type="password" placeholder="Repeat password" />
```

## Error Handling

### Flash Messages

This plugin makes use of October's [`Flash API`](https://octobercms.com/docs/markup/tag-flash). In order to display the error messages, you need to place the following snippet in your layout or page.

```twig
{% flash %}
    <div class="alert alert-{{ type == 'error' ? 'danger' : type }}">{{ message }}</div>
{% endflash %}
```

### AJAX Errors

The User plugin displays AJAX error messages in a simple ``alert()``-box by default. However, this might scare non-technical users. You can change the default behavior of an AJAX error from displaying an ``alert()`` message, like this:

```js
<script>
    $(window).on('ajaxErrorMessage', function (event, message){

        // This can be any custom JavaScript you want
        alert('Something bad happened, mate, here it is: ' + message);

        // This will stop the default alert() message
        event.preventDefault();

    })
</script>
```

### Checking Email/Username Availability

Here is a simple example of how you can quickly check if an email address / username is available in your registration forms. First, inside the page code, define the following AJAX handler to check the login name, here we are using the email address:

```php
public function onCheckEmail()
{
    $user = Auth::getProvider()->retrieveByCredentials([
        'email' => post('email')
    ]);

    return ['isTaken' => $user ? 1 : 0];
}
```

For the email input we use the `data-request` and `data-track-input` attributes to call the `onCheckEmail` handler any time the field is updated. The `data-request-success` attribute will call some jQuery code to toggle the alert box.

```html
<div class="form-group">
    <label>Email address</label>
    <input
        name="email"
        type="email"
        class="form-control"
        data-request="onCheckEmail"
        data-request-success="$('#loginTaken').toggle(!!data.isTaken)"
        data-track-input />
</div>

<div id="loginTaken" class="alert alert-danger" style="display: none">
    Sorry, that login name is already taken.
</div>
```
