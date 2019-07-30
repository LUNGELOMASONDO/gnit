<?php
    if(!function_exists(err_log)){
	    function err_log($error)
	    {
		    $filename = "error_log.txt";
		
	    	$myfile = fopen($filename, 'a') or die("Unable to log error: "     .$filename);
		    fwrite($myfile, date("h:i:sa") . " " . date("Y-m-d") . " " . $error .     "\n");
	    }
    }
?>