$(function () {
  $('#checkoutForm').on('submit', function (e) {
  /**
  * add new element to the checkout from, if not exist
  * Put Browser client info 
  */  
  if(!document.getElementById('3DS')) {
    var FN = document.createElement("input");
        FN.setAttribute("type", "hidden");
        FN.setAttribute("name", 'clientInfo');
        FN.setAttribute("id", '3DS');
    $('form').append(FN);
    $('#3DS').val(sendClientBrowserInfo());
  }
 
    // $('form')[0].reset(); // to Clean values by duble click
  e.preventDefault();
  $.ajax({
      type: 'post',
      url: 'makeRequest.php',
      data: $('#checkoutForm').serialize(),
      success: function (response) {
        console.log('Checkout form submited');  // Console Log
        
        $('#message').show();
        response = JSON.parse(response);
        if(response.status){
          $('#message-warning').hide();
          $('#message-info').hide();
          $('#message-success').show();
          $('#msgTitle').html("Data Sent");
          
          console.log("Error Code : "+response.data.error.code);  // Console Log
          
          if(response.data.error.code == 100 & response.data.payment.status == 15) {
            $('#conclusionMsg').append('<li>Your card have 3DS</li>');
            $('#conclusionMsg').append('<li>You will be redirecting to Bank Page</li>');
            
            /**
             * Step 2) 
             * Redirect to bank for Auth
             */
            buildFormRedirecAuthorize(response.data); 
          } else if (response.data.error.code == 0) {
            /**
             * For non 3DS card
             */
             $('#authenticationToken').val(response.data.customerAction.authenticationToken);
             $('#conclusionMsg').append('<li>Your card dosn\'t have 3DS!!!</li>');
             $('#conclusionMsg').append('<li>Payment procces countinuing for non 3DS card</li>');
             buildFormRedirecBackURL(response.data);

          } else if(response.data.error.code == 56) {
            $('#alertTitle').html("Duplicate Order");
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          }else if(response.data.error.code == 19) {
            $('#alertTitle').html("Error");
            $('#conclusionMsg').append('<li>Expire Card Error</li>');
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          } else if(response.data.error.code == 20) {
            $('#alertTitle').html("Error");
            $('#conclusionMsg').append('<li>Fonduri insuficiente</li>');
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          }  else if(response.data.error.code == 21 || response.data.error.code == 22) {
            $('#alertTitle').html("Error");
            $('#conclusionMsg').append('<li>CVV Error</li>');
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          } else if(response.data.error.code == 34) {
            $('#alertTitle').html("Error");
            $('#conclusionMsg').append('<li>Transaction not allowed</li>');
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          }else {
            $('#alertTitle').html("Info");
            $('#conclusionMsg').append('<li>'+response.data.error.message+'</li>');
          }
        }else{
          $('#message-success').hide();
          $('#message-info').hide();

          /**
           * assign error message to view
           */
          $('#msg-warning-title').html(response.message);
          $('#warning-status-msg').html('Your request is failed');
          $('#warning-type-code').html('the type of your error is :'+response.code);
          if (response.data !== undefined) {
            $('#warning-full-msg').html(response.message);
          }
          $('#message-warning').show();
        }
      },
      error: function (response) {
        console.log("NOT SEND AJAX");
        $('#message').show();
        $('#message-warning').show();
        
        console.log(response);
      }
    });
  });
});


/**
 * To create a form and send data to Bank for Authorize
 */
function buildFormRedirecAuthorize(response) {

  // Create a form Dynamically
  var formUniqueID = "authForm"+Math.floor(Math.random() * 1000);
  var form = document.createElement("form");
  
  form.setAttribute("method", "post");
  form.setAttribute("action", response.customerAction.url);
  form.setAttribute("id", formUniqueID);
  form.setAttribute("enctype", "multipart/form-data");


  for (const [key, value] of Object.entries(response.customerAction.formData)) {
    console.log(`${key}: ${value}`);
    var FN = document.createElement("input");
    FN.setAttribute("type", "hidden");
    FN.setAttribute("name", key);
    FN.setAttribute("value", value);
    form.appendChild(FN);
  }

document.getElementsByTagName("body")[0].appendChild(form);
document.getElementById(formUniqueID).submit();
}

/**
 * Redirect backUrl for non 3DS 
 */
 function buildFormRedirecBackURL(response) {

  $('#doPayment').prop('disabled', true);
  $('#paymentTitle').html('Congratulations, you successfully paid');

  document.getElementById("paymentResult").style.display = "block";
  $('#paymentAmount').html(response.payment.amount);
  $('#paymentCurrency').html(response.payment.currency);
  $('#ntpID').html(response.payment.ntpID);
  $('#token').html(response.payment.token);

 }