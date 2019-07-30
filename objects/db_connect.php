<?php
    if(!function_exists(dbconn)){
	    function dbconn()
    	{
    		$db = "gnitcoza_gnit";
	    	$servername = "localhost";
    		$username = "gnitcoza_MasondoAdmin";
    		$password = "waterinthedeepcallecho";

	    	try 
    		{
        		$conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        		// set the PDO error mode to exception
    		    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    	return $conn;
        		//echo "Connected successfully";
        	}
    		catch(PDOException $e)
        	{
    	    	$ERRCODE = "ERR001";
        		$message = $ERRCODE . ':DB connection failed: ' . $e->getMessage();
    			require 'log_error.php';
		    	err_log($message);
	    		die("$ERRCODE: Gnit is experiencing technical difficulties " . $e->getMessage());
        		//echo "Connection failed: " . $e->getMessage();
        	}
    	}
    }
?>