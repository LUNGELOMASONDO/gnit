<?php
	require 'objects/log_error.php';
	require 'objects/db_connect.php';
	require 'objects/session_life.php';
	require 'objects/results_data.php';
	
	session_start();
	session_life();
	
	header('Cache-Control: no-cache, no-store, must-revalidate');
	header('Pragma: no-cache');
	header('Expires: 0');
	
	function views_cap($upload_id)
	{
		$conn = dbconn();
		$stmt = $conn->prepare("INSERT INTO views (upld_id) VALUES (:id)");
		$stmt->bindParam(':id', $upload_id);
		$stmt->execute();
	}
	
	$upload_id = null;
	$admin_name = "";
	$prob_sol = null;
	$position = "";
	$timestamp = "00/00/0000 00:00 am";
	$module_code = "";
	$year = "";
	$heading = "";
	$file_location = "objects/files/33/gnit_file.jpeg";
	$file_ext = ".jpeg";
	$download_name = $heading . $file_ext;
	$searchkey = "";
	$num_downloads = 0;
	$num_flags = 0;
	$badge = "badge badge-success";
	$content_type = "image";
	$text_content = "<h1>Shuri method</h1><p>Make it make make sense</p><br/>Action";
	$num_comments = 7;
	
	
	if(isset($_GET['uploadid']))
	{
		$upload_id = $_GET['uploadid'];
		$prob_sol = is_problem_solution($upload_id);
		if($prob_sol == null)
		{
			header('Location:index.php');
			exit();
		}
		$admin_name = get_admin_name($upload_id);
		$position = get_position($upload_id);
		$timestamp = get_timestamp($upload_id);
		$module_code = get_upload_module($upload_id);
		$year = get_upload_year($upload_id);
		//$module_name = "";
		$heading = get_contents_heading($upload_id);
		$file_location = "objects/" . get_file($upload_id);
		$file_ext = get_file_ext($file_location);
		$download_name = $heading . "." . $file_ext;
		$searchkey = get_upload_key($upload_id, $prob_sol);
		$num_downloads = num_downloads($upload_id);
		$num_flags = num_flags($upload_id);
		$content_type = get_content_type($upload_id);
		$badge = set_badge_color($content_type);
		$text_content = get_content($upload_id);
		$num_comments = 7;		
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0" >
		<title> gnit: <?php echo $module_code ?> - <?php echo $heading; ?> </title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/sweetalert.min.js"></script>
		<link rel="stylesheet" href="css/styleform.css" />
		<script>
			<?php
				$file = fopen("js/results.js", "r");
				while(!feof($file))
				{
					echo fgets($file) . "\n";
				}
				fclose($file);
			?>
		</script>
	</head>
	
	<body>
<!-- nav -->
		<nav class="navbar navbar-expand-md navbar-white bg-white sticky-top">
			<div class="container-fluid">
				<a class="navbar-brand" href="index.php"> <img src="img/gnit-darkblue.jpg" alt="gnit" style="width:120px;" /> </a>
				<p><b><?php echo $module_code ?> - <?php echo $heading; ?></b></p>
			</div>
		</nav>
<!-- content -->
		<div class="container-fluid" id="content" style="padding-bottom:10%;">
			<div class="row">
				<div class="col-md-3">
					<div class="card bg-light mb-3" style="background:lightgray;">
						<div class="row">
							<div class="col-4">
								<small>Courtesy of:</small>
							</div>
							<div class="col-8">
								<small><?php echo $admin_name; ?></small>
								<br />
								<small><?php echo $position; ?></small>
								<br />
								<small><?php echo $timestamp; ?></small>
							</div>						
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<fieldset class="the-fieldset bg-light">
						<legend class="the-legend">Search Key: <?php echo $searchkey; ?> </legend>
						<p><u><?php echo $prob_sol; ?></u> <span class='<?php echo $badge; ?>'><?php echo $content_type; ?></span></p>
						<p>Year: <?php echo $year; ?></p>
						<?php
							if($content_type == "text" )
							{
								echo"<div class='card bg-default' id='text-content' style='overflow:auto;height:250px;' >" .
										$text_content .
									"</div>";
							} 
							elseif($content_type !== "doc")
							{
								echo"<button id='viewfile' class='btn btn-outline-dark' data-toggle='modal' data-target='#myModal'>
  										Click to view file
									</button>
									<div class='modal fade' id='myModal' tabindex='-1' role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>
  										<div class='modal-dialog'>
    										<div class='modal-content'>
     											<div class='modal-header'>
     												<h4 class='modal-title' id='myModalLabel'>" . $heading . "</h4> 
        											<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>×</button>
      											</div>
      											<div class='modal-body'>
        											<div style='text-align: center;'>
													<iframe src='" . $file_location . "'
													style='width:100%; height:500px;' frameborder='0'></iframe>
												</div>
      										</div>
      										<div class='modal-footer'>
       											<a class='btn btn-link bg-primary text-white' id='btn-download' href='" . $file_location . "' download='gnit_" . $download_name . "'>Download</a>
       											<button class='btn btn-danger' id='btn-flag' data-toggle='modal' data-target='#myFlagModal'>Flag for errors</button>
      										</div>
    									</div>
  									</div>
								</div>";
							}
					    ?>
						<br /><br />
						<div class="container container-fluid">
							<?php
								if($content_type != "text")
								{
									echo "<a class='btn btn-link bg-primary text-white' id='btn-download' href='" . $file_location. "' download='gnit_" . $download_name . "'>Download</a>";
								}
							?>	
							
							<button class="btn btn-danger" id="btn-flag" data-toggle='modal' data-target='#myFlagModal'>Flag for errors</button>
							<div class="modal fade" id="myFlagModal" tabindex="-1" role="dialog" aria-labelledby="myFlagModalLabel" aria-hidden="true">
  								<div class="modal-dialog">
    								<div class="modal-content">
     									<div class="modal-header">
     										<h4 class="modal-title" id="myFlagModalLabel"><?php echo $searchkey; ?>:<br /> Flag For Errors</h4> 
        									<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      									</div>
      									<div class="modal-body">
      										
        									<p><b>What best describes your disatisfaction with this material?</b></p>
        									<form id="flagform">
        										<input type="checkbox" id="copyright" name="copyright" value="copyright"/> Copyright Infringement <br />
        										<input type="checkbox" id="incomplete" name="incomplete" value="incomplete" /> Incomplete <br />
        										<input type="checkbox" id="incorrect" name="incorrect" value="incorrect" /> Incorrrect <br />
        										<input type="checkbox" id="visibility" name="visibility" value="visibility" /> Poor Visibility(Images) <br />
        										<input type="checkbox" id="information" name="information" value="information"/> Incorrect associative info(Searching) <br />
        										<input type="checkbox" id="duplicate" name="duplicate" value="duplicate" /> Already exists on Gnit <br />
        										<input type="checkbox" id="usefulness" name="usefulness" value="usefulness" /> It is not useful <br />
      										</form>
      									</div>
      									<div class="modal-footer">
       										<button class="btn btn-danger" id="btn-submitflag">Flag</button>
      									</div>
    								</div>
  								</div>
							</div>
							<?php
								if($content_type == "text")
								{
									echo "<p><small>Flags: " . $num_flags . "</small></p>";
								}
								else 
								{
									echo "<p><small>Downloads: " . $num_downloads . " <b>|</b> Flags: " . $num_flags . "</small></p>";
								}
							?>
						</div>
						<div class="card" id="comments">		
							<p><small><b>Comments:</b></small></p>				
							<div class="container container-fluid">
								<?php
									$conn = dbconn();
									$stmt = $conn->prepare("SELECT comment_time, thecomment FROM comment WHERE upld_id=:id ORDER BY comment_time DESC LIMIT 30;");
									$stmt->execute(array(":id" => $upload_id)); 
									$row = $stmt->fetchAll();
									
									foreach($row as $data)
									{
										$comment_time = strtotime($data['comment_time']);
										$comment_time = date("d/m/Y g:i a", $comment_time);
										$thecomment = $data['thecomment'];
										
										echo "<blockquote>
											  		<small><b>" . $comment_time . "</b></small>
													<br />
													" . $thecomment . "
											  </blockquote>";
									}
								?>				
							</div>
							<div class="card-header">
								<div><button class="btn btn-dark" disabled>Add a comment</button></div>
								<br />
								<span id="rchars">500</span> Character(s) Remaining
								<textarea class="form-control" id="writecomment" placeholder="500 character limit" rows="5"></textarea>
								<input type="submit" class="form-control bg-light" id="submit-comment" value="Submit" style="width:30%;"/>
							</div>
						</div>
					</fieldset>
				</div>
				<div class="col-md-3">
					
				</div>
			</div>
		</div>
		<!-- upload id for system use -->
		<p id="uploadid" style="height:0px;width:0px;visibility:hidden;"><?php echo $upload_id; ?></p>
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

<?php
	if($content_type == "text")
	{
		views_cap($upload_id);	
	}
?>

<!--<div class='text-left'><span class='<?php echo $badge; ?>'><?php echo $content_type; ?>"</span></div>-->

