# Authentication Component

## Overriding Functionality

Here is how you would override the `onSignin()` handler to log any error messages. Inside the page code, define this method:

```php
function onSignin()
{
    try {
        return $this->account->onSignin();
    }
    catch (Exception $ex) {
        Log::error($ex);
    }
}
```

Here the local handler method will take priority over the **account** component's event handler. Then we simply inherit the logic by calling the parent handler manually, via the component object (`$this->account`).
