<?php
require_once dirname(__DIR__) . '/autoload.php';

if (isset($_COOKIE['CASDev:token'])) {
    $manager = CASDevSessionManager::singleton();
    $manager->deleteSessionFor($_COOKIE['CASDev:token']);
    setcookie ("CASDev:token", "", time() - 3600);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Logged out</title>
    </head>
    <body>
        <h1>DEV-CAS: You have been successfully logged out.</h1>
        <h4>Return to your application to log back in</h4>
    </body>
</html>
