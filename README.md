WindowsPhone Push
==================

Windows Phone Push

This code is based on the code of [Rudy HUYN](http://www.rudyhuyn.com/).


## example

```php
<?php
require_once 'vendor/autoload.php';

try {
    $uri = "{the uri}";

    $notifier = new JildertMiedema\WindowsPhone\WindowsPhonePushNotification();
    $result = $notifier->pushToast($uri, "Test app", "Test message");

    var_dump($result);
} catch (Exception $e) {
    var_dump($e)
}
```
