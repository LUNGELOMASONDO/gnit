<?php
	require 'objects/log_error.php';
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
	
	function search_cap($search_string)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("INSERT INTO searches (search_string) VALUES (:search)");
		$stmt->bindParam(':search', $search_string);
		$stmt->execute();	
	}
	
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title> gnit </title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/results.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
	</head>
	<body>
<!-- Navigation -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
			
			<?php 
				if(isset($_GET['search']))
				{
					require 'objects/searchbar.php';
				}
				else
				{
					echo '<div class="btn-group-vertical dropdown dropleft float-right">
                                <div class="btn-group">
                                    <i class="material-icons dropdown-toggle" data-toggle="dropdown" style="font-size:48px;color:lightgrey">help</i>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="user-upload.php">Have a problem or solution to share?</a>
                                        <a class="dropdown-item" href="#">Help</a>
                                    </div>
                                </div>
                            </div>';
				}
			?>
			</div>
		</nav>
<!-- user content -->
		<div class="container-fluid" id="content" style="padding-bottom:10%;">
					<?php 
						try
						{
						$output = "";
						
						if(isset($_GET['search']))
						{
							$output = "<div class='row'>
								   		<div class='col-lg-4'>
					
										</div>
								  		<div class='col-lg-5' id='seach-section'>";
							
							$count = 0; // number of results seen by user
							$magnet = $_GET['search'];
							
							$output .=  "	<div class='jumbotron' style='padding-top:50px;padding-bottom:50px;'><h3><b>Search Results: </b><small>" . $magnet . "</small></h3></div>";
							$output .=  "	<div id='number-of-results'></div>";
							if($magnet !== "" && $magnet !== " ")
							{
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
										
										if($result[$id] == 0)
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
										
										if($result[$id] == 100000) // if a search key was entered
										{
											/*
											 * Search key entered
								 			 */
								 			$output .=  "<form action='result.php' method='GET'>
								 							<input type='text' name='uploadid' id='" . $id . "' style='visibility:hidden;width:0px;height:0px;'/>
								 							<script>
								 								$('#" . $id . "').val(" . $id . ");
								 							</script>
								 							<button type='submit' class='btn' style='width:100%; background-color:" . $output_block_color .";' id='" . $prob_sol . "_" . $id . "'>
					  											<div class='rec-title'>
																	<div class='text-left'><span class='" . $badge . "'>" . $content_type . "</span><small class='text-secondary'> " . $timestamp . "</small></div>
																	<span class='btn- btn-link'>" . $description  . " " . $problem_year . "</span>
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
											$count = "[search key]";
											break;
										}	
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
										$count = $count + 1;
									}
								}
								else 
								{
									$result .= "<script>
													swal('Gnit Search:', 'No results')
													.then((value) => {
  														window.location.replace('index.php');
													});
												</script>";	
									echo $result;					
								}
								search_cap($magnet); //record search 
							}
							$output .= "		<script>
													$('#number-of-results').html('Number of resources found: " . $count . "');
												</script>";
							$output .= "	</div>
												<div class='col-lg-3'>
					
												</div>				
									  </div>";							
							echo $output;						
						}
						else 
						{
						    //home
							require 'objects/content_section.php'; 
						}
						}//close try block
						catch(PDOException $e)
						{
							echo '<script>
              						swal({
  										title: "Gnit Search:",
										text: "' . $e->getMessage() . '",
  										icon: "warning",
 										button: "Go back",
									}).then((value) => {
										window.location.replace("index.php");
									});
								</script>';	
						}	
						catch(Exception $e)
						{
							echo '<script>
		              				swal({
  										title: "Gnit Search:",
										text: "' . $e->getMessage() . '",
  										icon: "warning",
 										button: "Go back",
									}).then((value) => {
										window.location.replace("index.php");
									});
								</script>';	
						}
					?> 
		</div>
<!-- Footer -->
		<footer class="footer small bg-light" style="position: fixed; left: 0; bottom: 0; width: 100%; height: 11%;">
			<div class="container">
				<div class="row">				
      				<div class="col-lg-7">
        				<span class="text-secondary">2019 &copy; <b>gnit</b> by LS Masondo. <br />In association with NWU's B!TS</span>
      				</div>
      				<div class="col-lg-5 text-right">
      					<a href="admin-login.php">Administrator Portal</a>
      				</div>
      			</div>
      		</div>
    	</footer>
	</body>
</html>

<?php
	exit();
?>