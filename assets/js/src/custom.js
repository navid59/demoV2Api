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
      // data: $('form').serialize(),
      data: $('#checkoutForm').serialize(),
      success: function (response) {
        console.log('form was submitted');
        
        $('#message').show();
        response = JSON.parse(response);
        if(response.status){
          $('#message-warning').hide();
          $('#message-info').hide();
          $('#message-success').show();
          $('#msgTitle').html("Data Sent");
          
          
          console.log(response.data); // TEMPORARY
          console.log(response.data.error.code);  // TEMPORARY

          if(response.data.error.code == 100 & response.data.payment.status == 15) {
            $('#authenticationToken').val(response.data.customerAction.authenticationToken);
            $('#paReq').val(response.data.customerAction.formData.paReq);
            $('#ntpID').val(response.data.payment.ntpID);
            $('#conclusionMsg').append('<li>Your card have 3DS</li>');
            $('#conclusionMsg').append('<li>You will be redirecting to Bank Page</li>');
            
            /**
             * Step 2) Sandbox/Auton
             * preparing for redirect
             */
            var backUrl = window.location.origin + "/demoV2/backAuth.php"; 
            doRedirectSandboxAuthorize(response.data.customerAction.formData.paReq, backUrl);            
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

// JS make redirect to an internal page
function doRedirectSandboxAuthorize(paReq, backUrl) {
  var url = "doAuth.php?paReq="+paReq+"&backUrl="+backUrl;
  window.location.href = url;
  return true;
}

// Display Logs Real Time
function getLog() {
  $.ajax({
      url: window.location.origin + '/demoV2/logs/realtimeLog.log',
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
      url: window.location.origin + '/demoV2/logs/ipnLog.log',
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
      url: window.location.origin + '/demoV2/logs/returnLog.log',
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
    url: window.location.origin + '/demoV2/clearLog.php',
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