<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/vendor/autoload.php';
include_once('lib/log.php');

/**
 * Load .env 
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$setRealTimeLog = 
            [
                "Do Auth"    =>  "Is hitting - will redirect",
                "What is " => "Send Parameter (paReq , backUrl) to the Bank"
            ];
log::setRealTimeLog($setRealTimeLog);
?>
<!doctype html>
<html lang="en">
    <?php include_once("assets/theme/inc/header.inc"); ?>
    <body class="bg-light">
        <div class="container">
            <?php include_once("assets/theme/inc/topNav.inc"); ?>
            <div class="row">
                <?php include_once("assets/theme/doAuthForm.php"); ?>
            </div>
        </div>

        <?php include_once("assets/theme/inc/footer.inc"); ?>
        <script>
            (function() {
                // To auto submit the page to Temporary Sandbox Bank simulator
                // alert("Do Auth - make Form & Submit to Bank - Temporary Sandbox Bank simulator");
                console.log('Do Auth - make Form & Submit to Bank - Temporary Sandbox Bank simulator');
                document.getElementById('authForm').submit();
            })();
        </script>
    </body>
</html>