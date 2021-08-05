$(document).ready(() => {
  // to Manage the Tabs & defulte Tab
  let url = location.href.replace(/\/$/, "");
 
  if (location.hash) {
    var hash = url.split("#");
    $('#myTab a[href="#'+hash[1]+'"]').tab("show");
    url = location.href.replace(/\/#/, "#");
    history.replaceState(null, null, url);
    setTimeout(() => {
      $(window).scrollTop(0);
    }, 400);
  } 
   
  $('a[data-toggle="tab"]').on("click", function() {
    let newUrl;
    const hash = $(this).attr("href");
    if(hash == "#home") {
      newUrl = url.split("#")[0];
    } else {
      newUrl = url.split("#")[0] + hash;
    }
    newUrl += "/";
    history.replaceState(null, null, newUrl);
  });


  getLog();       // Real time Log
  getIpnLog();    // Real time Log IPN
  getReturnLog(); // Real time Log success Page
});


$(function () {
  $('#checkoutForm').on('submit', function (e) {
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
            $('#authenticationToken').val(response.data.customerAction.authenticationToken);
            $('#conclusionMsg').append('<li>Your card dosn\'t have 3DS!!!</li>');
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
            $('#warning-full-msg').html(response.data.message);
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

  // Create a form synamically
  var form = document.createElement("form");
  form.setAttribute("method", "post");
  form.setAttribute("action", response.customerAction.url);
  form.setAttribute("id", "authForm");
  form.setAttribute("enctype", "multipart/form-data");


  for (const [key, value] of Object.entries(response.customerAction.formData)) {
    console.log(`${key}: ${value}`);
    var FN = document.createElement("input");
    FN.setAttribute("type", "text");
    FN.setAttribute("name", key);
    FN.setAttribute("value", value);
    form.appendChild(FN);
  }

document.getElementsByTagName("body")[0].appendChild(form);
document.getElementById('authForm').submit();
}

// Display Logs Real Time
function getLog() {
  $.ajax({
      url: window.location.origin + '/demoV2/example/logs/realtimeLog.log',
      dataType: 'text',
      success: function(text) {
          $("#containerDiv").html(text);
          setTimeout(getLog, 3000); // refresh every 3 seconds
      }
  })
}

// Display IPN Logs Real Time
function getIpnLog() {
  $.ajax({
      url: window.location.origin + '/demoV2/example/logs/ipnLog.log',
      dataType: 'text',
      success: function(text) {
          $("#containerDivIpnLog").html(text);
          setTimeout(getIpnLog, 3000); // refresh every 3 seconds
      }
  })
}

// Display Success page Logs Real Time
function getReturnLog() {
  $.ajax({
      url: window.location.origin + '/demoV2/example/logs/returnLog.log',
      dataType: 'text',
      success: function(text) {
          $("#containerDivReturnLog").html(text);
          setTimeout(getReturnLog, 300000); // refresh every 3 seconds
      }
  })
}

// Remove Logs file
function cleanLogFile(logType) {
  // alert("Log Type : "+logType);
  $.ajax({
    type: 'post',
    url: window.location.origin + '/demoV2/example/clearLog.php',
    data: {'logType': logType},
    success: function(response) {
      response = JSON.parse(response);
      if(response.status){
        if(logType ==1 ){
          $('#logMessage-success').show();
          $('#logSuccessMessage').html(response.msg);
          $('#logMessage-warning').hide();
        }

        if(logType ==2 ){
          $('#ipnLogMessage-success').show();
          $('#ipnLogSuccessMessage').html(response.msg);
          $('#ipnLogMessage-warning').hide();
        }
        
      }else{
        if(logType ==1 ){
          $('#logMessage-success').hide();
          $('#logMessage-warning').show();
          $('#logWarningMessage').html(response.msg);
        }

        if(logType ==2 ){
          $('#ipnLogMessage-success').hide();
          $('#ipnLogMessage-warning').show();
          $('#ipnLogWarningMessage').html(response.msg);
        }
        
      }
    },
    error: function (response) {
      alert("Log Ajax Not Worked");
      console.log(response);
    }
  })
}