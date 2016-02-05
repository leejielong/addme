<?php

	require("common.php"); //connect to database

	if(!empty($_POST)) //run registration code if form is submitted
	{
		//Check password validity. Check if password was entered and if both passwords match.
		if(empty($_POST['password'])) //if no password was entered
		{
			$errPw="Please enter a password"; //tell user if user left pw field blank
		}
		elseif($_POST['password']!==$_POST['password2'])//if password doesnt match retyped password
		{
			$errPw="Passwords do not match"; //tell user that passwords dont match
		}
		//Check email validity, now that password is valid
		else
		{
			if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //check that user entered valid email
			{
				$errEmail="Invalid Email Address"; //otherwise tell user that email is invalid
			}

			//Check email availability, now that email and password is valid.
			else
			{

				// check email to ensure that it does not already exist
				$query = "SELECT
							1
							FROM users
							WHERE
							email = :email
							";

				//define query parameters
				$query_params = array(':email' => $_POST['email']);

				//execute the query
				try
				{
					$stmt = $db->prepare($query);
					$result = $stmt->execute($query_params);
				}
				catch(PDOException $ex)
				{
					die("Failed to run query2: " . $ex->getMessage());
				}

				$row = $stmt->fetch(); //retrieve query data

				if($row) //if a matching email is found, dont permit the registration
				{
					$errEmail="The email address is already registered.";
				}

				//we have checked password and email to be valid. now we can create new user.
				else
				{
					$query = "INSERT INTO users (
											name,
											email,
											password,
											salt,
											phone,
											facebookurl,
											instagramurl,
											twitterurl,
											linkedinurl

											)VALUES(
											:name,
											:email,
											:password,
											:salt,
											:phone,
											:facebookurl,
											:instagramurl,
											:twitterurl,
											:linkedinurl
											)
											";

					// use salt cryptography to protect the database from brute force or rainbow table attacks.
					// generate a random 8 byte number in hexadecimal
					$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647));

					//hash password with salt
					$password = hash('sha256', $_POST['password'] . $salt);

					// perform further hashing to make it harder for attackers to compute the hash
					for($round = 0; $round < 65536; $round++)
					{
						$password = hash('sha256', $password . $salt);
					}

					//define insertion parameters. store only the hashed version of the password.
					$query_params = array(
									':name' => $_POST['name'],
									':email' => $_POST['email'],
									':password' => $password,
									':salt' => $salt,
									':phone' => $_POST['phone'],
									':facebookurl' => '',
									':instagramurl' => '', 
									':twitterurl' => '',
									':linkedinurl' => ''
									);

					//execute query
					try
					{
						$stmt = $db->prepare($query);
						$result = $stmt->execute($query_params);
					}
					catch(PDOException $ex)
					{
						die("Failed to run query4: " . $ex->getMessage());
					}

					//save userdata into session
					$_SESSION['user']['name'] = $_POST['name'];
					$_SESSION['user']['email'] = $_POST['email'];
					$_SESSION['user']['password'] = $password;
					$_SESSION['user']['salt'] = $salt;
					$_SESSION['user']['phone'] = $_POST['phone'];
					$_SESSION['user']['facebookurl'] = '';
					$_SESSION['user']['instagramurl'] = '';
					$_SESSION['user']['twitterurl'] = '';
					$_SESSION['user']['linkedinurl'] = '';

					// send user back to login page after registering
					header("Location: account.php");
					die("Redirecting to account.php"); //must end the script when you go to another page.
				}
			}
		}
	}
?>

<!--html code-->
<html lang="en">
<head>
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>

<style>
h1,h2,h3,h4,h5,h6,p,li,a{
font-family: 'Open Sans', sans-serif;
}
body {
    background-color: #fff8e1;
}

</style>

<title>Register</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

 <!-- Obtain latest version of jquery from CDN -->
	<script src="http://code.jquery.com/jquery-latest.js"></script>
 <!-- jQuery must be imported before materialize -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.96.1/js/materialize.min.js"></script>  

	<!-- Compiled and minified CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/css/materialize.min.css">
	<!-- Compiled and minified JavaScript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.0/js/materialize.min.js"></script>

</head>
<body>

<!-- navigation bar -->
	 <nav class="teal">
		<div class="container">
			<a href="#" data-activates="nav-mobile" class="button-collapse top-nav full hide-on-large-only"><i class="mdi-navigation-menu"></i></a>
		</div>
		<div class="nav-wrapper">
			<ul class="left hide-on-med-and-down">
				<li><a href="index.php">Login</a></li>
				<li><a href="register.php">Sign Up</a></li>
			</ul>
		</div>
			<ul class="side-nav" id="nav-mobile"">
				<li class="center-align"><a href="index.php">Login</a></li>
				<li class="center-align"><a href="register.php">Sign Up</a></li>
			</ul>
		</div>
	</nav>

<!-- page title and registration form-->
<div class="container">
	<h3 class="center-align">Sign up for Add.me</h3>
	<h6 class="center-align">An easy way to connect to your friends</h6>
	<form class="col s12 ""action="register.php" role="form" method="post">

				<?php echo "<h5 class=\"center-align\">$errName$errEmail$errPw$errVaultid</h5>"; ?>

		<div class="row">
			<div class="input-field col s12">
				<input id="name" name="name" type="text" class="validate">
				<label class="active" for="text" >Name</label>
			</div>
		</div>

		<div class="row">
			<div class="input-field col s12">
				<input id="email" name="email" type="email" class="validate">
				<label class="active" for="email" data-error="Invalid" data-success="">Email</label>
			</div>
		</div>

		<div class="row">
			<div class="input-field col s12">
				<input id="phone" name="phone" type="text" class="validate">
				<label class="active" for="text" >Mobile No.</label>
			</div>
		</div>

		<div class="row">
			<div class="input-field col s12">
				<input id="password" name="password" type="password" class="validate">
				<label class="active" for="password">Password</label>
			</div>
		</div>
		<div class="row">
			<div class="input-field col s12">
				<input id="password2" name="password2" type="password" class="validate">
				<label class="active" for="password">Re-Enter Password</label>
			</div>
		</div>

	 <div class="row">
			<div class="input-field col s12">
				<button class="btn teal darken-1 btn-large waves-effect waves-light" style="width: 100%" type="submit" name="action">Register</button>
			</div>
		</div>
	</form>
</div>

<!--script for collapsing navigation bar when window size is small-->
<script>
 $(document).ready(function(){
	 // Activate the side menu
	 $(".button-collapse").sideNav();
	});
</script>

</body>
</html>

