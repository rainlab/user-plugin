# Authentication Component

## Redirection

The `redirect` property specifies a page name to redirect to after a successful sign in. When it is empty, no redirect is performed unless the form supplies one.

```ini
[authentication]
redirect = "home"
```

This allows an AJAX login to be performed without exposing the redirect URL in the page markup. The form supplies no redirect of its own, and the component handles it based on the property.

```html
<form data-request="onLogin">
    <input name="login" />
    <input name="password" type="password" />
    <button type="submit">Sign in</button>
</form>
```

A redirect posted by the form still takes priority, and can be disabled by posting a `redirect` value of `0`.

## Overriding Functionality

Here is how you would override the `onLogin()` handler to log any error messages. Inside the page code, define this method:

```php
function onLogin()
{
    try {
        return $this->account->onLogin();
    }
    catch (Exception $ex) {
        Log::error($ex);
    }
}
```

Here the local handler method will take priority over the **account** component's event handler. Then we simply inherit the logic by calling the parent handler manually, via the component object (`$this->account`).
