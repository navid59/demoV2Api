<div class="col" style="padding-top:15px;">
    <form id="authForm" action="<?=$authorize->bankUrl;?>" method="POST" enctype="application/x-www-form-urlencoded" class="" novalidate>
    <div class="col-md-8 order-md-1">
        <h4 class="mb-3">Authorize Information</h4>
        <p>The <b>API KEY, Token paReq, Back Url</b> as parameters, will send to authorize action</p>
        <p>page will post the data automatically to authorize endpoint. </p>
        <div class="mb-3">
            <!-- <label for="apiKey">API KEY</label> -->
            <input type="hidden" class="form-control" id="apiKey" name="apiKey" placeholder="String - Length(0-255)" value="<?=$authorize->apiKey;?>" required>
            <div class="invalid-feedback" style="width: 100%;">
                API Key is required.
            </div>
            
        </div>
      
        <div class="mb-3">
            <!-- <label for="paReq">Token paReq</label> -->
            <input type="hidden" class="form-control" id="paReq" name="paReq" placeholder="String - Length(0-255)" value="<?=$authorize->paReq;?>" required >
            <div class="invalid-feedback" style="width: 100%;">
                paReq ID is required.
            </div>
        </div>

        <div class="mb-3">
            <!-- <label for="paReq">Token paReq</label> -->
            <input type="hidden" class="form-control" id="backUrl" name="backUrl"  value="<?=$authorize->backUrl;?>" placeholder="Enter Back URL">
        </div>

      <hr class="mb-4">
      <!-- Button, is removed, because, the form will be submit automatically -->
      <!-- <button class="btn btn-primary btn-lg btn-block" id="doAuthToBank" type="submit">Continue to Auth to the Bank</button> -->
    </form>
  </div>
</div>