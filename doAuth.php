<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/vendor/autoload.php';
include_once('lib/log.php');
include_once('lib/request.php');
include_once('lib/authorize.php');

/**
 * Load .env 
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$setRealTimeLog = 
            [
                "authorizeAction"  => "Authorize action Is hitting",
                "authorize"        => "Send Parameter (paReq , backUrl) to the Bank"
            ];
log::setRealTimeLog($setRealTimeLog);

/**
 * Set authorize parameters
 * @param apiKey,paReq,backUrl
 * the apiKey,backUrl can be set static or read from DB, File, ...
 * you have the paReq token from response of start action 
 */
$authorize = new authorize();
$authorize->apiKey = 'Uxf3OY--rDK3Qae8CiJJUlAcuRJFp7tzGY4M8KocQaCGyfEqUGhGskv0';
$authorize->backUrl = 'http://35.204.43.65/demoV2/backUrl.php';
if(isset($_GET['paReq']) && !is_null($_GET['paReq'])) {
    $authorize->paReq = $_GET['paReq'];
} else {
    throw new \Exception('paReg is not defined');
    exit;
}

?>
<!doctype html>
<html lang="en">
    <?php include_once("assets/theme/inc/header.inc"); ?>
    <body class="bg-light">
        <div class="container">
            <?php include_once("assets/theme/inc/topNav.inc"); ?>
            <div class="row">
                <?php include_once("assets/theme/authForm.php"); ?>
            </div>
        </div>
        <?php include_once("assets/theme/inc/footer.inc"); ?>
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