<?php
	
	require 'includes/functions.php';
	session_start();
	
	$message = '<div class="alert alert-danger text-center">';
	$valid = 0;
	
	$postid = $_POST['postid'];
	$username = trim($_POST['username']);
	$title = trim($_POST['title']);
	$comment = trim($_POST['comment']);
	$priority = trim($_POST['priority']);
	
	if(!isset($_SESSION['loggedin']))
	{
		header('Location: index.php');
		exit();
	}

	if(count($_POST) > 0)
	{
		if(!preg_match('/^[a-z]([a-z]|[0-9]){6}([a-z]|[0-9])*[0-9]+$/i', $username))
		{
			$message .= 'Invalid Username!<br>';			
		} else {
			$valid += 1;
		}
		if(!preg_match("/^[a-z ]+$/i", $title))
		{
			$message .= 'Invalid Title!<br>';
		} else {
			$valid += 1;
		}
		if(!preg_match('/^[a-z0-9 ,\.\?!]+$/i', $comment))
		{
			$message .= 'Invalid Comment!<br>';
		} else {
			$valid += 1;
		}
		if($valid == 3)
		{		
			$lines = file('posts.txt');

			if($lines != false)
			{
				// w truncates the file
				$fp = fopen('posts.txt', 'w');

				// comb through all existing lines
				foreach($lines as $line)
				{
					$pieces = preg_split("/\|/", $line);

					if($pieces[0] == $postid && $pieces[1] == $username)
					{
						$string = $postid . "|" . $username . "|" . $title . "|" . $comment . "|" . $priority . PHP_EOL;
						// echo "NEW - " . $string . "<br>";
						fwrite($fp,$string);
						continue;
					}

					// echo "OLD - " . $line . "<br>";
					fwrite($fp, $line); // re-write this line
				}

				fclose($fp);
			}
			
			$message .= 'Entry Saved!';	
		}
	}
	$message .= '</div>';
	
?>
<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>

<div id="wrapper" style="margin-bottom: 85px;">

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Assignment 3
                </h1>
                <hr/><br>
                <div>
                    <?php echo $message; ?>
                </div><br>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <a href="posts.php" class="btn btn-default btn-lg">
                    <i class="fa fa-list"></i> Posts
                </a>
            </div>
        </div>

    </div>
</div>

<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>
