<?php
	require 'objects/session_life.php';
	require 'objects/db_connect.php';
	require 'objects/search.php';
	require 'objects/upload.php';
	require 'objects/problem.php';
	require 'objects/solution.php';
	require 'objects/results_data.php';
	
	session_start();
	session_life();
	
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	
	$magnet = "";
	
	if(isset($_SESSION['content-administrator']) && isset($_POST['search']))
	{
		$magnet = $_POST['search'];
	}
	else
	{
		header('Location:admin-login.php');
	}	
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title>Gnit: Duplicate check</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/bootstrap-select.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/results.js"></script>
		<script src="js/sweetalert.min.js"></script>
	</head>
	
	<body>
<!-- nav -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
				<h1 class="page-desc"><b>Duplicate Check</b></h1>
			</div>
		</nav>
<!-- content -->
		<div class="container-fluid" id="content" style="padding-bottom:10%;">
			<div class="row">
				<div class="col-lg-4">
					
				</div>
				<div class="col-lg-4">
					<div class='jumbotron' style='padding-top:50px;padding-bottom:50px;'><h3><b>Search Results: </b><small><?php echo $magnet ?></small></h3></div>
					<div id='number-of-results'></div>
					<?php
						$count = 0; //number of results
						if($magnet !== "" && $magnet !== " ")
						{
							$output = "";
							$result = search($magnet);	
							if(isset($result))
							{
								$result_key_arr = array_keys((array)$result);
								$conn = dbconn();
								/*
								* GET UPLOAD INFO FOR RESULTS
						    	 */
						 		$output_block_color = "lightgray"; // used to alternate the color of the results blocks
								foreach($result_key_arr as $id)
								{
									//set search result blocks values
									$prob_sol = is_problem_solution($id); 
									
									if($result[$id] < 10)
									{
										/*
										 * RESULT IS IRRELEVANT WITH A SCORE OF 0 FOR CURRENT SEARCH
										 */
										 continue;
								    }
									
									if(!isset($prob_sol))
									{
										/*
										 * IF BY ANY CHANCE AN UPLOAD EXISTS WITHOUT AN ACCOMPANYING SOLUTION/PROBLEM
										 * DO NOT DISPLAY AS A SEARCH RESULT
										 */
										continue;	 
									}
									//set search result blocks values
									$content_type = get_content_type($id);
									$badge = set_badge_color($content_type);
									$description = get_contents_heading($id);
									$key_code = get_upload_key($id, $prob_sol);
									$num_downloads = num_downloads($id);
									$num_flags = num_flags($id);
									$problem_year = problem_year($id, $prob_sol); // returns empty string if not a problem
									$timestamp = get_timestamp($id);
									/*
									 * result output block
									 */																	
									$output .=  "<form action='result.php' method='GET'>
													<input type='text' name='uploadid' id='" . $id . "' style='visibility:hidden;width:0px;height:0px;'/>
							 						<script>
								 						$('#" . $id . "').val(" . $id . ");
								 					</script>
													<button type='submit' class='btn' style='width:100%; background-color:" . $output_block_color .";' id='" . $prob_sol . "_" . $id . "'>
					  									<div class='rec-title'>
															<div class='text-left'><span class='" . $badge . "'>" . $content_type . "</span><small class='text-secondary'> " . $timestamp . "</small></div>
															<span class='btn- btn-link'>" . $description . "</span>
														</div>
														<div class='rec-info'>
															<p>
																<span class='text-muted'>" . $prob_sol . " <b>|</b> Key: " . $key_code . "</span>
																<br/>
																<span><small>Downloads: " . $num_downloads . "  |  Flags: " . $num_flags . "</small></span>
															</p>
														</div>
													</button>
												</form>";
										// used to alternate the color of the results blocks
									if($output_block_color == "lightgray")
									{
										$output_block_color = "white";
									}
									else 
									{
										$output_block_color = "lightgray";	
									}
									
									if($count == 5)
									{
										break;
									}
									$count = $count + 1;											
								}
							}
							else
							{
								$result .= "<script>
												swal('Gnit Search:', 'No duplicates')
													.then((value) => {
  														window.location.replace('index.php');
												});
											</script>";	
								echo $result;	
							}
							$output .= "		<script>
													if(" . $count . " == 0)
													{
														$('#number-of-results').html('No potential duplicates detected');
													}
													else 
													{
														$('#number-of-results').html('" . $count . " Potential duplicate(s). Please check them.');	
													}
												</script>";
							echo $output;	
						}
					?>
				</div>
				<div class="col-lg-4">
					
				</div>
			</div>
		</div>
<!-- footer -->		
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 11%;">
			<div class="container">
				<div class="row">				
      				<div class="col-lg-7">
        				<span class="text-secondary"><?php echo date('Y'); ?> &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      			</div>
      		</div>
    	</footer>
	</body>
</html>
