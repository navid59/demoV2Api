<?php 
class bank {
    
    function __construct(){        
    }

    static function validateBackUrl() {
        /**
         * to Verify bank URL
         * may content localhost:8080, can check it and replace localhost with Sandbox url
         */

        // Temporary, just replaced by static String
        // The following URL is a simulation of BANK 3D Verify PAGE
        
        return ("https://secure.sandbox.netopia-payments.com/sandbox/authorize");
    }
}