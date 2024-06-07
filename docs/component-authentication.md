# Authentication Component

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
