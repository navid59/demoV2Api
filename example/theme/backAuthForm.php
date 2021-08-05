<div class="col" style="padding-top:15px;">
    <div class="alert alert-primary" role="alert">
        <?=$paymentResultArr->message?>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h4 class="mb-3">Payment Status</h4>
            
            <div class="row">
                <div class="col-md-6 mb-6">
                    <label for="cc-expiration-year">Error Code</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->error->code;?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Error ->  Code
                    </div>
                </div>
                
                <div class="col-md-6 mb-6">
                    <label for="cc-expiration-year">Error Message</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->error->message;?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Error ->  Message 
                    </div>
                </div>
            </div>

            <hr>
            <h4 class="mb-3">Payment Details</h4>

            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="cc-expiration-year">Amount</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->payment->amount?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Payment ->  Amount
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="cc-expiration-year">Currency</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->payment->currency?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Payment ->  Currency
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label for="cc-expiration-year">ntpID</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->payment->ntpID?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Payment ->  Amount
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="cc-expiration-year">status</label>
                    <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->payment->status?>" placeholder="" disabled >
                    <div class="invalid-feedback">
                        Data -> Payment ->  Currency
                    </div>
                </div>
            </div>

            <?php
            if(isset($paymentResultArr->data->payment->token)){
                ?>
                    <div class="row">
                        <div class="col">
                            <label for="cc-expiration-year">Token</label>
                            <input type="text" class="form-control" id="" name="" value="<?=$paymentResultArr->data->payment->token?>" placeholder="" disabled >
                            <div class="invalid-feedback">
                                Data -> Payment ->  Token
                            </div>
                        </div>
                    </div>  
                <?php
            }
            ?> 

            <hr>
            <h4 class="mb-3">paRes Token from Bank by Post Method</h4>
            <?php
            if(isset($verifyAuth->paRes)){
                ?>
                    <div class="row">
                        <div class="col">
                            <label for="cc-expiration-year">paRes Token</label>
                            <input type="text" class="form-control" id="" name="" value="<?=$verifyAuth->paRes;?>" placeholder="" disabled >
                            <div class="invalid-feedback">
                                Post Method -> From Bank -> paRes
                            </div>
                        </div>
                    </div>  
                <?php
            }
            ?>        
        </div>
        <div class="col-md-4">
            <div class="card">
                <?php
                switch($paymentResultArr->data->payment->status){
                    case 3:
                        echo '
                        <div class="alert alert-success" role="alert">
                            Congratulations, you successfully paid.
                        </div>
                        ';
                        echo '
                        <div class="card-body">
                            <h5 class="card-title">Total amount : </h5>
                            <p class="card-text">'.$paymentResultArr->data->payment->amount.' '.$paymentResultArr->data->payment->currency.'</p>
                        </div>
                        ';
                    break;
                    case 5:
                    default:
                    echo '
                        <div class="alert alert-info" role="alert">
                            There is an issue with your request - verify the status
                        </div>
                        ';
                }
                ?>
                <div class="card-body">
                    <h5 class="card-title"></h5>
                    <p class="card-text">to do more test click the following button and try new payment in Demo mode.</p>
                    <a href="<?=getenv('PROJECT_SERVER_ADDRESS').getenv('PROJECT_BASE_ROOT')?>" class="btn btn-primary stretched-link">continue shopping</a>
                </div>
            </div>
        </div>
    </div>
</div>