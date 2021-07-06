<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/vendor/autoload.php';
include_once('classes/log.php');


/**
 * Load .env 
 * To read Logo , ... from .env
 */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

log::setRealTimeLog(array('CheckoutPage' => getenv('LOG_TXT_CHECKOUT') ? getenv('LOG_TXT_CHECKOUT') : 'Checkout is hitting' ));
?>
<!doctype html>
<html lang="en">
    <?php include_once("theme/inc/header.inc"); ?>
    <body class="bg-light">
        <div class="container">
            <?php include_once("theme/inc/topNav.inc"); ?>
            <?php include_once("theme/inc/nav.inc"); ?>
            <div class="row">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <?php include_once("theme/checkout.php"); ?>
                    </div>
                    <div class="tab-pane fade" id="getStatus" role="tabpanel" aria-labelledby="getStatus-tab">Check a payment status</div>
                    <div class="tab-pane fade" id="expirePayment" role="tabpanel" aria-labelledby="expirePayment-tab">Expire a Payment</div>
                    <div class="tab-pane fade" id="realTimeLog" role="tabpanel" aria-labelledby="realTimeLog-tab">
                        <div class="panel panel-primary" id="result_panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Real time Log</h3>
                            </div>
                            <div class="list-group">
                                <span id="containerDiv">Real Time Log</span>
                            </div>
                            <hr>
                                <button class="btn btn-secondary btn-lg btn-block" onclick="cleanLogFile(1)">Remove Log</button>
                                <div id="logMessage-success" class="alert alert-success" style="display: none;">
                                    <p id="logSuccessMessage"></p>
                                </div>
                                <div id="logMessage-warning" class="alert alert-warning" style="display: none">
                                    <p id="logWarningMessage"></p>
                                </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="confirmURL" role="tabpanel" aria-labelledby="confirmURL-tab">
                        <div class="panel panel-primary" id="result_panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">IPN Log</h3>
                            </div>
                            <div class="list-group">
                                <span id="containerDivIpnLog">IPN real time log</span>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn-secondary btn-lg btn-block" onclick="cleanLogFile(2)">Remove Log</button>
                        <div id="ipnLogMessage-success" class="alert alert-success" style="display: none;">
                            <p id="ipnLogSuccessMessage"></p>
                        </div>
                        <div id="ipnLogMessage-warning" class="alert alert-warning" style="display: none">
                            <p id="ipnLogWarningMessage"></p>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="returnURL" role="tabpanel" aria-labelledby="returnURL-tab">
                        <div class="panel panel-primary" id="result_panel">
                            <div class="panel-heading">
                                <h3 class="panel-title">Success Page Log</h3>
                            </div>
                            <div class="list-group">
                                <span id="containerDivReturnLog">Real Time Log</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="document" role="tabpanel" aria-labelledby="document-tab">
                        <?php include_once("theme/apiDocument.php"); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once("theme/inc/footer.inc"); ?>
    </body>
</html>