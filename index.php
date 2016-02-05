<?php
    require("common.php"); //connect to database
    $submitted_email = ''; //variable to show previously typed email if wrong email is submitted

    if(!empty($_POST)) //run verification checks if input is submitted via POST
    {
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) //check that user entered valid email using filter_var function in php
        {
	    $errEmail='Please enter a valid E-Mail address'; //trigger error text for email
        }

	if(!$errEmail) //execute further verification if email is valid
	{

            // Retreive user information from the database using their email.
            $query = "SELECT name,email,password,salt,phone,facebookurl,instagramurl,twitterurl,linkedinurl
                FROM users
                WHERE email = :email";

            // set parameter values
            $query_params = array(
                ':email' => $_POST['email']);

            try
            {
                // Execute query
                $stmt = $db->prepare($query);
                $result = $stmt->execute($query_params);
            }
            catch(PDOException $ex)
            {
                die("query1 failed" . $ex->getMessage());
            }

            $login_ok = false; //login status- turn it to true if user account is verified to be valid.

            $row = $stmt->fetch(); //retrieve user data from the query. If $row is false, that means the account is not valid.

            if($row) //if user data is found
            {
	        // hash the submitted password with the salt in database to check whether it matches with the hashed password stored in the database.
                $check_password = hash('sha256', $_POST['password'] . $row['salt']);
                for($round = 0; $round < 65536; $round++)
                {
                    $check_password = hash('sha256', $check_password . $row['salt']); //hash password 65537 times more
                }

                if($check_password === $row['password'])//if the new hashed password is identical to the hashed password in the database, user is allowed to login
                {
                    $login_ok = true; //allow user to enter
                }
            }

            if($login_ok) //if login successful, time to set up a session
            {
	        //remove salt and password from $row before using $row to set up session
                unset($row['salt']);
                unset($row['password']);

                // store user data into the session at the index 'user'.
                $_SESSION['user'] = $row; //in private page, only allow users with valid sessions.

                // send user to private page.
                header("Location: home.php");
                die("Redirecting to: home.php");
            }
            else //if login is unsuccessful
            {
	        $errID="Invalid Email/Password"; //error text to tell user login has failed
	        //show user the previously submitted email
                $submitted_email = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8'); //use htmlentities to prevent XSS attacks.
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

<title>add.me</title>
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

<!-- page title and form for login submission -->
<div class="container">
  <h3 class="center-align">Add.Me</h3>
  <h6 class="center-align">Add your friends across all platforms in a single gesture</h6>
  <form class="col s12 ""action="index.php" role="form" method="post">
    <div class="row">
      <div class="input-field col s12">
        <input id="email" name="email" type="email" class="validate">
        <label class="active" for="email" data-error="Invalid" data-success="">Email</label>
      </div>
    </div>

    <div class="row">
      <div class="input-field col s12">
        <input id="password" name="password" type="password" class="validate">
        <label class="active" for="password">Password</label>
        <?php echo "<p class='text-danger'>$errID</p>"; ?>
        <button class="btn teal darken-1 btn-large waves-effect waves-light" style="width: 100%" type="submit" name="action">Login</button>
      </div>
    </div>
  </form>
</div>

<!-- script for collapsing the menu on the navigation bar when browser window is small -->
<script>
 $(document).ready(function(){
   // Activate the side menu
   $(".button-collapse").sideNav();
  });

</script>
</body>
</html>
