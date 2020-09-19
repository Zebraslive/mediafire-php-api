# mediafire-php-api
Login and access Mediafire API without api key.

```
<?php
include('mf.php');
$mediafire = new mf('email@gmail.com', 'password'); //login and save session for later.
$session_token = $mediafire->session_token;
?>
```
