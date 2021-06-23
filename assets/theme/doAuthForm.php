<?php 
session_start();
include_once('lib/bank.php');
// die(print_r($_SESSION)); // Hear have Session all the Time
?>
<div class="col" style="padding-top:15px;">
    <form id="authForm" action="<?=bank::validateBackUrl();?>" method="POST" enctype="application/x-www-form-urlencoded" class="" novalidate>
    <div class="col-md-8 order-md-1">
        <h4 class="mb-3">Config Information</h4>
        <div class="mb-3">
            <label for="apiKey">API KEY</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">API Key</span>
                </div>
                <input type="text" class="form-control" id="apiKey" name="apiKey" placeholder="String - Length(0-255)" value="<?=$_SESSION["apiKey"];?>" required disabled>
                <div class="invalid-feedback" style="width: 100%;">
                    API Key is required.
                </div>
            </div>
        </div>

      
        <div class="mb-3">
            <label for="paReq">Token paReq</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Req Token</span>
                </div>
                <input type="text" class="form-control" id="paReq" name="paReq" placeholder="String - Length(0-255)" value="<?=$_GET['paReq']?>" required >
                <div class="invalid-feedback" style="width: 100%;">
                    paReq ID is required.
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label for="backUrl">Back Url<span class="text-muted"></span></label>
            <input type="text" class="form-control" id="backUrl" name="backUrl"  value="<?=$_GET['backUrl'];?>" placeholder="Enter Back URL">
        </div>

      <!-- Button, is removed, because, the form will be submit automatically -->
      
      <hr class="mb-4">
      <button class="btn btn-primary btn-lg btn-block" id="doAuthToBank" type="submit">Continue to Auth to the Bank</button>
     
    </form>
  </div>
</div>