<?php
require_once dirname(__DIR__) . '/autoload.php';

if (isset($_COOKIE['CASDev:token'])) {
    $manager = CASDevSessionManager::singleton();
    $manager->deleteSessionFor($_COOKIE['CASDev:token']);
    setcookie ("CASDev:token", "", time() - 3600);
}
?>
<!DOCTYPE html>
<html
  lang="en"
  xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta charset="utf-8" />
    <title>looged out CAS CAS CAS!</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
    </head>
    <body>
        <!-- Content Area -->
        <div class="body-area">
            <h1>login to ubc's <blink>FAKE CASE!</blink> &trade;</h1>
            <marquee>LOGIN TO CAS&Eacute; DEV!!  fake cas is the best CASe!!!11!</marquee>
            <img class="bin-bashy" src="https://roboj.as.it.ubc.ca/images/bin-bashy-sm.jpg" />
            <div class="content-area">
                <h2 style="color: chartreuse">goodbye firend!! <blink>u r my bestie! :D</blink></h2>
                <p style="color: chartreuse">u loggeredd out!!!!1! :(</p>
            </div>
        </div>
    </body>
</html>
