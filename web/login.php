<?php
require_once dirname(__DIR__) . '/autoload.php';
$conf = CASDevConfig::singleton();

$service = (!empty($_GET['service'])) ? $_GET['service'] : '';
$token = (!empty($_GET['token'])) ? $_GET['token'] : '';
$user = (!empty($_POST[$conf->userAttributeId])) ? $_POST[$conf->userAttributeId] : '';
$password = (!empty($_POST['password'])) ? $_POST['password'] : '';
$attributes = (!empty($_POST['attributes'])) ? $_POST['attributes'] : [];

if (empty($service) && !empty($_POST['redir'])) {
    $service = $_POST['redir'];
}
if (empty($token) && !empty($_POST['token'])) {
    $token = $_POST['token'];
}

if (!empty($user) && $password === $conf->password) {

    $attributes[$conf->userAttributeId] = $user;

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
<html
  lang="en"
  xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="utf-8" />

        <title>Fake CAS Login Page</title>
        <link rel="stylesheet" type="text/css" href="style.css"/>
    </head>
    <body>
        <!-- Content Area -->
        <div class="body-area">
            <h1>login to <blink>FAKE CASE!</blink> &trade;</h1>
            <marquee>LOGIN TO CAS&Eacute; DEV!!  fake cas is the best CASe!!!11!</marquee>
            <img class="bin-bashy" src="bin-bashy-sm.jpg" />
            <div class="content-area">
                <h2>Fake CAS <small class="text-muted">login page (for da local dev r0x0r environments)</small></h2>
                <?php
                if (!empty($_POST)) {
                    if (empty($user)) {
                        echo '<p class="alert alert-danger">username can\'t be blankx0r!</p>';
                    } else {
                        echo '<p class="alert alert-danger">wrongzz (super secure) password!1 get it right plz ktkhx!</p>';
                    }
                }
                ?>
                <form class="container-fluid mt-4" action="login.php" method="post">
                    <div class="form-row mb-2">
                        <label for="user" class="text-md-right pt-2 col-2"><?=htmlspecialchars($conf->userAttributeName)?>:</label>
                        <input class="form-control col-3"  type="text" id="user" name="<?=htmlspecialchars($conf->userAttributeId)?>" value="<?=htmlspecialchars($user);?>">
                    </div>
                    <?php foreach ($conf->attributes as $id => $name) { ?>
                    <?php $defaultValue = (isset($conf->defaultAttributes[$id])) ? $conf->defaultAttributes[$id] : ''; ?>
                    <div class="form-row mb-2">
                        <label class="text-md-right pt-2 col-2" for="attribute-<?=htmlspecialchars($id)?>"><?=htmlspecialchars($name)?>:</label>
                        <input class="form-control col-3"  type="text" id="attribute-<?=htmlspecialchars($id)?>" name="attributes[<?=htmlspecialchars($id)?>]" value="<?=htmlspecialchars(isset($attributes[$id]) ? $attributes[$id] : $defaultValue)?>" />
                    </div>
                    <?php } ?>
                    <div class="form-row mb-2">
                        <label for="password" class="text-md-right pt-2 col-2"><b>CASDev</b> Password:</label>
                        <input class="form-control col-3"  type="password" id="password" name="password">
                    </div>
                    <input type="hidden" name="redir" value="<?=htmlspecialchars($service);?>" />
                    <input type="hidden" name="token" value="<?=htmlspecialchars($token);?>" />
                    <input type="submit" value="submit" class="btn btn-dark offset-2" />
                </form>
            </div>
        </div>
    </body>
</html>
