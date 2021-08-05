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


  getLog();       // to loade Real time Log
  getIpnLog();    // to loade IPN log
  getReturnLog(); // to loade success page Log
});


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