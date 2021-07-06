<?php 
class log {
    
    function __construct(){
    }

    static function setLog($code, $log, $arrLog) {
        switch ($code) {
            case 100:
                self::setBackendLog($log);
                self::setRealTimeLog($arrLog);
            break;
            case 200:
            case 400:
            case 404:
            case "xx":
                self::setRealTimeLog($arrLog);
            break;
            default:
                self::setBackendLog($log);
        }
    }

    // to clean logs file
    static function cleanLogFile($fileName) {
        $logPath = 'logs/'.$fileName.'.log';
        if(file_exists($logPath)){
            file_put_contents($logPath, "");
            return true;
        }else{
            return false;
        }
    }

    static function setBackendLog($log) {
        if(is_null($log))
            return false;
            
        $logPoint = rand(1,1000).date(" - H:i:s - ")." | ";
        ob_start();                     // start buffer capture
        echo $logPoint;
        var_dump( $log );               // dump the values
        $contents = ob_get_contents();  // put the buffer into a variable
        ob_end_clean();
           file_put_contents('logs/api.log', $contents , FILE_APPEND | LOCK_EX);
    }

    static function setRealTimeLog($arrLog) {
        if(is_null($arrLog))
            return false;

        $logPoint = '<li class="list-group-item">';
        $logPoint .= date(" - H:i:s - ")." ";
        ob_start();                     // start buffer capture
        
        foreach($arrLog as $key => $val) {
            $logPoint .= " <b>".$key ." : </b> ". $val;
        }
        $logPoint .= "</li>
        ";
        echo $logPoint;
        $contents = ob_get_contents();  // put the buffer into a variable
        ob_end_clean();
           file_put_contents('logs/realtimeLog.log', $contents , FILE_APPEND | LOCK_EX);
    }

    static function setIpnLog($log) {
        if(is_null($log))
            return false;
            
        $logPoint = rand(1,1000).date(" - H:i:s - ")." | ";
        ob_start();                     // start buffer capture
        echo $logPoint;
        var_dump( $log );               // dump the values
        $contents = ob_get_contents();  // put the buffer into a variable
        ob_end_clean();
           file_put_contents('logs/ipnLog.log', $contents , FILE_APPEND | LOCK_EX);
    }
}