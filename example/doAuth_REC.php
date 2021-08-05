<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/vendor/autoload.php';
include_once('classes/log.php');
include_once('../lib/request.php');
include_once('../lib/authorize.php');

/**
 * Load .env 
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

/**
 * Set authorize parameters
 * @param apiKey,paReq,backUrl
 * the apiKey,backUrl can be set static or read from DB, File, ...
 * you have the paReq token from response of start action 
 */
$authorize = new Authorize();
$authorize->apiKey  = 'Uxf3OY--rDK3Qae8CiJJUlAcuRJFp7tzGY4M8KocQaCGyfEqUGhGskv0'; // Put Your key here
$authorize->backUrl = 'http://35.204.43.65/demoV2/example/backUrl.php';
$authorize->paReq   = $_GET['paReq'];
$authorize->bankUrl = $_SESSION['authorizeUrl'];

$authorize->validateParam();

?>
<!doctype html>
<html lang="en">
    <?php include_once("theme/inc/header.inc"); ?>
    <body class="bg-light">
        <div class="container">
            <?php include_once("theme/inc/topNav.inc"); ?>
            <div class="row">
                <?php include_once("theme/authForm.php"); ?>
            </div>
        </div>
        <?php include_once("theme/inc/footer.inc"); ?>
        <script>
            (function() {
                /**
                 * To auto submit the auth form
                 */
                document.getElementById('authForm').submit();
            })();
        </script>
    </body>
</html>