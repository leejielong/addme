<?php
    require("common.php"); //connect to database
    include "BarcodeQR.php"; 

    if(empty($_SESSION['user'])) //check if user is logged in
    {
        header("Location: index.php"); //if user is not logged in, send user to main page
        die("Redirecting to index.php");
    }

    if(!empty($_POST))
    {
        $filename = $_SESSION['user']['name']; 
        $filename .= ".png";
               
        //delete old qr code
        unlink($filename);

        if($_POST['switch1']=='on')
        {
            $text .= $_SESSION['user']['phone'];
        }
        else
        {
            $text .= "";
        }
        $text .= "\n";

        if($_POST['switch2']=='on')
        {
            $text .= $_SESSION['user']['email'];
        }
        else
        {
            $text .= "";
        }
        $text .= "\n";

        if($_POST['switch3']=='on')
        {
            $text .= $_SESSION['user']['facebookurl'];
        }
        else
        {
            $text .= "";
        }
        $text .= "\n";

        if($_POST['switch4']=='on')
        {
            $text .= $_SESSION['user']['instagramurl'];
        }
        else
        {
            $text .= "";
        }
        $text .= "\n";

        if($_POST['switch5']=='on')
        {
            $text .= $_SESSION['user']['linkedinurl'];
        }
        else
        {
            $text .= "";
        }

        // set BarcodeQR object 
        $qr = new BarcodeQR(); 

        //echo $text;

        // create URL QR code 
        $qr->text($text); 

        //save QR code image
        $qr->draw(300, $filename);

        //header("Location: generate.php");
        //die("Redirecting to generate.php");
    }

    //echo $text

?>

<html lang="en">
<head>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link href='https://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">

<style>

h1,h2,h3,h4,h5,h6,p,li,a{
font-family: 'Open Sans', sans-serif;
}

body {
    background-color: #fff8e1;
}

.material-icons.green600 { color: #00c853; }
.material-icons.grey600 { color: #9e9e9e; }

p.wrap {
    word-wrap: break-word;
}

</style>

<title>Home</title>
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

<!-- script for collapsing the menu on the navigation bar when browser window is small -->
<script>
 $(document).ready(function(){
   // Activate the side menu
   $(".button-collapse").sideNav();
  });

</script>


<!-- page title and form for login submission -->
<div class="container">
    <h3 class="center-align"><?php echo $_SESSION['user']['name']?>'s contacts</h3>
    <h6 class="center-align">Select the contacts you wish to share</h6>
    <br>

<?php

if(!empty($_POST))
{
?>
    <div class="row">
      <div class="col s12 m8 offset-m2">
        <div class="card">
          <div class="card-image">
            <img class=" responsive-img center-block" src="<?php echo $filename ?>">
          </div>
        </div>
      </div>
    </div>
<?php
}
?>

<form "action="home.php" role="form" method="post">
    <ul class="collapsible" data-collapsible="expandable">

    <li>
        <div class="collapsible-header">
            <div class="row">
                <div class = "col s6">
                    <i class="fa fa-whatsapp"></i>
                    Whatsapp
                </div>
                <div class="col s6">
                    <div class="switch right-align">
                        <label>
                            Off
                            <input type="checkbox" name="switch1" checked="checked">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
      <div class="collapsible-body white"><p class="wrap"><?php echo $_SESSION['user']['phone']?></p></div>
    </li>

    <li>
        <div class="collapsible-header">
            <div class="row">
                <div class = "col s6">
                    <i class="material-icons">mail_outline</i>
                    Email
                </div>
                <div class="col s6">
                    <div class="switch right-align">
                        <label>
                            Off
                            <input type="checkbox" name="switch2" checked="checked">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapsible-body white"><p class="wrap"><?php echo $_SESSION['user']['email']?></p></div>
    </li>

<?php
//show dropdown only if facebook is connected.
if(!($_SESSION['user']['facebookurl']==""))
{
?>
    <li>
        <div class="collapsible-header">
            <div class="row">
                <div class = "col s6">
                    <i class="fa fa-facebook-square"></i>
                    Facebook
                </div>
                <div class="col s6">
                    <div class="switch right-align">
                        <label>
                            Off
                            <input type="checkbox" name="switch3" checked="checked">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapsible-body white"><a href="<?php echo $_SESSION['user']['facebookurl']?>"><p class="wrap"><?php echo $_SESSION['user']['facebookurl']?></p></a>
        </div>
    </li>
<?php
}
?>

<?php
//show dropdown only if instagram is connected.
if(!($_SESSION['user']['instagramurl']==""))
{
?>

    <li>
        <div class="collapsible-header">
            <div class="row">
                <div class = "col s6">
                    <i class="fa fa-instagram"></i>
                    Instagram
                </div>
                <div class="col s6">
                    <div class="switch right-align">
                        <label>
                            Off
                            <input type="checkbox" name="switch4" checked="checked">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapsible-body white"><a href="<?php echo $_SESSION['user']['instagramurl']?>"><p class="wrap"><?php echo $_SESSION['user']['instagramurl']?></p></a>
        </div>
    </li>
<?php
}
?>

<?php
//show dropdown only if linkedin is connected.
if(!($_SESSION['user']['instagramurl']==""))
{
?>
    <li>
        <div class="collapsible-header">
            <div class="row">
                <div class = "col s6">
                    <i class="fa fa-linkedin-square"></i>
                    LinkedIn
                </div>
                <div class="col s6">
                    <div class="switch right-align">
                        <label>
                            Off
                            <input type="checkbox" name="switch5" checked="checked">
                            <span class="lever"></span>
                            On
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="collapsible-body white"><a href="<?php echo $_SESSION['user']['linkedinurl']?>"><p class="wrap"><?php echo $_SESSION['user']['linkedinurl']?></p></a>
        </div>
    </li>
<?php
}
?>
</ul>
    </br>

    <button class="btn teal darken-1 btn-large waves-effect waves-light" style="width: 100%" type="submit" name="action">Generate QR Code</button>

</form>


</body>
</html>

   