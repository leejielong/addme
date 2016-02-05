<?php
    require("common.php"); //connect to database

    if(empty($_SESSION['user'])) //check if user is logged in
    {
        header("Location: index.php"); //if user is not logged in, send them to main page
        die("Redirecting to index.php");
    }

    if(!empty($_POST)) //check if form is submitted
    {
        //if email is empty, proceeed without update
        if($_POST['email'] == '')
        {
            header("Location: account.php"); //redirect to private page
            die("Redirecting to account.php");
        }

		//compare pw1 and pw2
		if($_POST['password']!==$_POST['password2'])
		{
	    	$errPw="Passwords do not match";
		}
    	//if both passwords match, move on to check email address
		else
    	{
    		// Make sure the user entered a valid E-Mail address
        	if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        	{
        		$errEmail="Invalid E-Mail Address";
        	}

        	//if email is valid, move on to check if it is already in the database
			else
	    	{

        		if($_POST['email'] != $_SESSION['user']['email']) //if email is different, check with database
        		{
	    	    	// define query
            		$query = "SELECT 1
                    	FROM users
                    	WHERE email = :email"; //:email is special token

 	            	// define query parameter values
        	    	$query_params = array(':email' => $_POST['email']);

            		try
            		{
                		// execute query
               			$stmt = $db->prepare($query);
                		$result = $stmt->execute($query_params);
            		}
            		catch(PDOException $ex)
           	    	{
                		die("Failed to run query 1: " . $ex->getMessage());
            		}

           	    	// Retrieve results (if any)
            		$row = $stmt->fetch();
            		if($row) //if account with that email is found
            		{
                		$errEmail="This E-Mail address is already in use";
                	}
        		}

				if(!$errEmail) //if email has no issues, process password
				{
        	   		//rehash it regardless of whether it is the same password or not.
        	    	if(!empty($_POST['password'])) //if password field is not empty
        	    	{
            			$salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); //regenerate salt
            			$password = hash('sha256', $_POST['password'] . $salt); //rehash password with salt
            			for($round = 0; $round < 65536; $round++)
            			{
                	    	$password = hash('sha256', $password . $salt);
            	        }
        	    	}
        	    	else
        	    	{
            			// if a new password was not entered we will not update the password field
            			$password = null;
                		$salt = null;
        	    	}

		   			// split the update query to separate email and password update. In some cases, only email is changed,
		   			// so there is no need to update password.
       	    		$query = "UPDATE users
           	    		SET
						email = :email
	   	    			";

                    // If the user is changing their phone number, we extend the query to include the phone update.
                    if($_POST['phone'] !== null)
                    {
                        $query .= "
                        , phone = :phone
                        "; //concatenate query string with phone number
                    }

	           		// If the user is changing their password, we extend the query to include the password and salt update.
       	    		if($password !== null)
       	    		{
          				$query .= "
						, password = :password
						, salt = :salt
						"; //concatenate query string with pw and salt
       	    		}

      	 	   		$query .= "
		   				WHERE
		    			name = :name
		    			"; //concatenate query string with WHERE to update only 1 account

                   	// set query parameters
                   	$query_params = array(
                       	':email' => $_POST['email'],
                       	':name' => $_SESSION['user']['name'],);

                    if($_POST['phone'] !== null)
                    {
                        $query_params[':phone'] = $_POST['phone'];
                    }

                   	//add password and salt to the update if password was entered
                    if($password !== null)
                    {
                      	$query_params[':password'] = $password;
                       	$query_params[':salt'] = $salt;
                   	}

       	    		try
       	    		{
           				// execute query
           				$stmt = $db->prepare($query);
            			$result = $stmt->execute($query_params);
            		}
       	    		catch(PDOException $ex)
       	    		{
           	       		die("Failed to run query 2: " . $ex->getMessage());
       	    		}

        	   		$_SESSION['user']['email'] = $_POST['email']; //update session data
					$_SESSION['user']['phone'] = $_POST['phone'];

       	   			header("Location: account.php"); //redirect to private page
                    die("Redirecting to account.php");
				}
	    	}
		}
    }

?>

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

<title>Edit Account</title>
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
        <li><a href="home.php">Home</a></li>
        <li class="center-align"><a href="account.php">Link Profiles</a></li>
        <li><a href="editaccount.php">Account</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </div>
      <ul class="side-nav" id="nav-mobile"">
        <li class="center-align"><a href="home.php">Home</a></li>
        <li class="center-align"><a href="account.php">Link Profiles</a></li>
        <li class="center-align"><a href="editaccount.php">Account</a></li>
        <li class="center-align"><a href="logout.php">Logout</a></li>
      </ul>
    </div>
  </nav>

<!--account update form-->
</br>
<div class="container">
  <h4 class="center-align"><?php echo htmlentities($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8'); ?>'s account</h4>
  <h6 class="center-align">Update your account details</h6>
  <form class="col s12 ""action="editaccount.php" role="form" method="post">
        <?php echo "<h5 class=\"center-align\">$errEmail$errPw</h5>"; ?>

    <div class="row">
      <div class="input-field col s12">
        <input id="email" name="email" type="email" class="validate">
        <label class="active" for="email" data-error="Invalid" data-success="">New Email</label>
      </div>
    </div>

    <div class="row">
      <div class="input-field col s12">
        <input id="phone" name="phone" type="text" class="validate">
        <label class="active" for="text">New Mobile No.</label>
      </div>
    </div>

    <div class="row">
      <div class="input-field col s12">
        <input id="password" name="password" type="password" class="validate">
        <label class="active" for="password">New Password</label>
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
        <button class="btn teal darken-1 btn-large waves-effect waves-light" style="width: 100%" type="submit" name="action">Next</button>
      </div>
    </div>
  </form>
</div>

<!--script to collapse navigation bar when browser window is small-->
<script>
 $(document).ready(function(){
   // Activate the side menu
   $(".button-collapse").sideNav();
  });
</script>
</body>
</html>
