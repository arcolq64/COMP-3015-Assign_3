<?php

	require 'includes/functions.php';

	// $pswd = "iamadmin2";
	// $sltp = md5($pswd . SALT);
	// echo $sltp;
	// exit();
		
	if(count($_POST) > 0 && $_GET['from'] == 'login')
	{
		// assume not found for regular users and admin
		$found = false;  // regular users
		$admnf = false;	 // admin

		$user = trim($_POST['user']);
		$pass = trim($_POST['password']);
		$admnf = findAdmin($user, $pass);
		
		if($admnf == true) {
			
			session_start();
			$_SESSION['loggedin'] = true;
			$_SESSION['username'] = $user;
			$_SESSION['adminuser'] = true;
			header('Location: thankyou.php?type=login&username='.$user);
			
		} else {
			
			if(checkUsername($user))
			{
				$found = findUser($user, $pass, 'username');
			}
			elseif(checkPhoneNumber($user))
			{
				$found = findUser($user, $pass, 'phone');
			}

			if($found)
			{
				session_start();
				$_SESSION['loggedin'] = true;
				$_SESSION['username'] = $user;
				$_SESSION['adminuser'] = false;
				header('Location: thankyou.php?type=login&username='.$user);
			}
			else
			{
				setcookie('error_message', 'Login not found! Try again.');
				header('Location: login.php');
			}
		}
		
		exit();
		
	}
	elseif(count($_POST) > 0 && $_GET['from'] == 'signup')
	{
		$check = checkSignUp($_POST);

		if($check !== true)
		{
			setcookie('error_message', $check);
			header('Location: signup.php');
		}
		else
		{
			$userfound = false; 
			$username = trim($_POST['username']);
			
			//-----------------------------------------------
			// If users.txt exists ...
			// Determine if a Username/Password entry exists.
			// The aim is to avoid duplicate entries.
			//-----------------------------------------------
			
			$file_pointer = './users.txt';
			if(file_exists($file_pointer)) {
			
				$lines = file('users.txt');
				
				// for each line of the $lines array, alias it to $line
				foreach ($lines as $line)
				{
					// Split $line by the regex pattern - just a @ in this case
					// the pieces from the split are returned in an array and stored in $pieces
					$pieces = preg_split('/\|/', $line);
					
					if($username == trim($pieces[0])) {	// Valid Username
						$userfound = true;				// If true, record is found
					}
				}					
			}
			
			if($userfound == false) {
				if(saveUser($_POST))
				{
					session_start();
					$_SESSION['loggedin'] = true;
					$_SESSION['username'] = filterUserName(trim($_POST['username']));
					$_SESSION['adminuser'] = false;
					header('Location: thankyou.php?type=signup&username='.trim($_POST['username']));
				}
				else
				{
					setcookie('error_message', 'Unable to sign up at this time.');
					header('Location: signup.php');
				}
			} else {
				setcookie('error_message', 'Username not available.');
				header('Location: signup.php');
			}
		}

		exit();
	}

	// should never reach here but if we do, back to index they go
	header('Location: index.php');
	exit();

?>