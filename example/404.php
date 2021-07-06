<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/vendor/autoload.php';
/**
 * Load .env 
 * To read Logo , ... from .env
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
?>
<!doctype html>
<html lang="en">
    <?php include_once("theme/inc/header.inc"); ?>
    <body class="bg-light">
        <div class="container">
            <?php include_once("theme/inc/topNav.inc"); ?>
            <div class="row">
                <h1>Error 404</h1>
            </div>
            <div class="row">
                <h5>May the session is expired</h5>
            </div>
        </div>
        <?php include_once("theme/inc/footer.inc"); ?>
    </body>
</html>
