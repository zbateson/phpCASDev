<?php
require_once dirname(__DIR__) . '/autoload.php';
$conf = CASDevConfig::singleton();

$service = (!empty($_GET['service'])) ? $_GET['service'] : '';
$token = (!empty($_GET['token'])) ? $_GET['token'] : '';
$user = (!empty($_POST['user'])) ? $_POST['user'] : '';
$password = (!empty($_POST['password'])) ? $_POST['password'] : '';
$attributes = (!empty($_POST['attributes'])) ? $_POST['attributes'] : $conf->defaultAttributes;

if (empty($service) && !empty($_POST['redir'])) {
    $service = $_POST['redir'];
}
if (empty($token) && !empty($_POST['token'])) {
    $token = $_POST['token'];
}

if (!empty($user) && $password === $conf->password) {
    
    $manager = CASDevSessionManager::singleton();
    $session = new CASDevSession();
    
    setcookie('CASDev:token', $token);
    $session->token = $token;
    $session->user = $user;
    $session->time = time();
    $session->attributes = $attributes;
    $manager->loginAs($session);

    if (!empty($service)) {
        header("Location: $service");
    } else {
        // incorrect redirect - should be fully-qualified
        header('Location: logged-in.html');
    }
    exit;
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta content-type="text/html" />
        <title>CAS Development Login</title>
    </head>
    <body>
        <h1>CAS Dev Authentication</h1>
        <?php
        if (!empty($_POST)) {
            if (empty($user)) {
                echo '<p>You must type a username</p>';
            } else {
                echo '<p>The password you entered is invalid</p>';
            }
        }
        else {
            echo '<p>Type in any user account to logins</p>';
        }
        ?>
        <form action="login.php" method="post">
            <div>
                <label for="user">CAS User:</label>
                <input type="text" id="user" name="user" value="<?=htmlspecialchars($user);?>">
            </div>
            <div>
                <label for="password"><b>CASDev</b> Password:</label>
                <input type="password" id="password" name="password">
            </div>
            <div>
                <label for="attributes">Attributes:</label>
                <input type="text" id="attributes" name="attributes" value="<?=htmlspecialchars($attributes);?>" />
            </div>
            <input type="hidden" name="redir" value="<?=htmlspecialchars($service);?>" />
            <input type="hidden" name="token" value="<?=htmlspecialchars($token);?>" />
            <input type="submit" value="submit" />
        </form>
    </body>
</html>
