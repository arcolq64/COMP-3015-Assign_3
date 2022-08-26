<?php

	require 'includes/functions.php';
	$message = '';
	session_start();

	if(!isset($_SESSION['loggedin']))
	{
		header('Location: index.php');
		exit();
	}

	if(count($_POST) > 0)
	{
		$check = checkPost($_POST, $_SESSION['username']);
		if($check !== true)
		{
			$message = '<div id="message" class="alert alert-danger text-center">'
				. $check .
			'</div>';
		}
		else
		{
			savePost($_POST);
		}
	}

	$posts = getAllPosts();

?>
<!DOCTYPE html>
<html>
<head>
    <title>COMP 3015</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet">
	<meta http-equiv='cache-control' content='no-cache'>
</head>
<body>

<div id="wrapper" style="margin-bottom: 85px;">

    <div class="container">

        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <h1 class="login-panel text-center text-muted">
                    COMP 3015 Assignment 3
                </h1>
                <hr/>
                <?php echo $message; ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <button class="btn btn-default" data-toggle="modal" data-target="#newPost"><i class="fa fa-comment"></i> New Post</button>
                <a href="logout.php" class="btn btn-default pull-right"><i class="fa fa-sign-out"> </i> Logout</a>
                <hr/>
            </div>
        </div>

        <?php
            foreach($posts as $post)
            {
                echo '
				<div class="row">
					<div class="col-md-6 col-md-offset-3">
						<div class="panel '.getPriorityTag(trim($post[4])).'">
							<div class="panel-heading">
								<span>
									'.$post['2'].'
								</span>
								<span class="pull-right text-muted">';
								
									if($_SESSION['username'] == $post[1])
									{
										echo '
										<a class="" href="delete.php?id='.$post['0'].'">
											<i class="fa fa-trash"></i> Delete
										</a>&nbsp;';
									}
				
									if($_SESSION['adminuser'] == true) 
									{
										
										echo '
										<a class="" href="delete.php?id='.$post['0'].'">
											<i class="fa fa-trash"></i> Delete
										</a>&nbsp;';	
		?>	
										<a id="<?php echo $post['0']; ?>" class="edit_data" data-toggle="modal" data-target="#editPost">
											<i class="fa fa-edit"></i> Edit
										</a>
		<?php
									}

								echo '
								</span>
							</div>
							<div class="panel-body">
								<p class="text-muted">
								</p>
								<p>
									'.$post['3'].'
								</p>
							</div>
							<div class="panel-footer">
								<p>
									'.$post['1'].'
								</p>
							</div>
						</div>
					</div>
				</div>';
            }
        ?>

    </div>
</div>

<div id="newPost" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form role="form" method="post" action="posts.php">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">New Post</h4>
        </div>
        <div class="modal-body">
                <div class="form-group">
                    <input class="form-control disabled" type="text" placeholder="Username" name="username" value="<?php echo $_SESSION['username']; ?>">
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input class="form-control" type="text" placeholder="" name="title">
                </div>
                <div class="form-group">
                    <label>Comment</label>
                    <textarea class="form-control" rows="3" name="comment"></textarea>
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select class="form-control" name="priority">
                        <option value="1">Important</option>
                        <option value="2">High</option>
                        <option value="3">Normal</option>
                    </select>
                </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" value="Post!"/>
        </div>
    </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="editPost" class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
    <form id="editForm" role="form" method="post" action="edit.php">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Edit Post</h4>
        </div>
        <div class="modal-body">
			<input id="postid" type="hidden" name="postid">
			<div class="form-group">
				<input id="username" class="form-control disabled" type="text" placeholder="Username" name="username">
			</div>
			<div class="form-group">
				<label>Title</label>
				<input id="title" class="form-control" type="text" placeholder="" name="title">
			</div>
			<div class="form-group">
				<label>Comment</label>
				<textarea id="comment" class="form-control" rows="3" name="comment"></textarea>
			</div>
			<div class="form-group">
				<label>Priority</label>
				<select id="priority" class="form-control" name="priority">
					<option value="1">Important</option>
					<option value="2">High</option>
					<option value="3">Normal</option>
				</select>
			</div>
        </div>
		<div id="response"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <input type="submit" class="btn btn-primary" name="submit" onClick="empty()" value="Submit!"/>
        </div>
    </div><!-- /.modal-content -->
    </form>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</body>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>  
 $(document).ready(function(){ 
	
	$(document).on('click', '.edit_data', function(){
		
		var postid = $(this).attr("id");
		$.get('posts.txt', { "_": $.now() }, function(data) {
            var lines = data.split("\n");
			$.each(lines, function(n,data) {
				var lines = data.split("|");
				if(postid==lines[0]) {
					$('input[name="postid"]').val(lines[0]);
					$('input[name="username"]').val(lines[1]);
					$('input[name="title"]').val(lines[2]);
					$('textarea[name="comment"]').val(lines[3]);
					var selct = parseInt(lines[4]);
					$('[name=priority]').val(selct);
					// alert(lines[1]+"@"+lines[2]+"@"+lines[3]+"@"+lines[4]);
				}	
            });
		});
	});
	
	// $('#form').submit(function() {
    // if ($.trim($("#email").val()) === "" || $.trim($("#user_name").val()) === "") {
    //    alert('you did not fill out one of the fields');
    //    return false;
    // }
	// });
	
	$('#editForm').on("submit", function(event){ 
		
		e.preventDefault();
		
		var username = $('#username').val();
		var title = $('#title').val();
		var comment = $('#comment').val();
		var priority = $('#priority').val();

		if($('#username').val() == '')  
		{  
			alert("Username is required"); 
			return false;
		}  
		else if($('#title').val() == '')  
		{  
			alert("Title is required"); 
            return false;			
		}  
		else if($('#comment').val() == '')  
		{  
			alert("A comment is required");
            return false;			
		} 
		else
		{  

			var data = $("#editForm").serialize();
			
			$.ajax({
				type: "POST",
				url: "edit.php",
				data: data,
				// data: { username : username, title : title, comment : comment, priority : priority },  // passing the values
				success: function(response) {
					console.log(response);
					$("#editPost").modal('hide');
				},
				error: function(xhr, status, error){
					var errorMessage = xhr.status + ': ' + xhr.statusText
					alert('Error - ' + errorMessage);
				}	
			});
			
		}
		
	});  
});  
</script>
</html>
