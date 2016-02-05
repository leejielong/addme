<?php
    require("common.php"); //connect to database

    if(empty($_SESSION['user'])) //check if user is logged in
    {
        header("Location: index.php"); //if user is not logged in, send user to main page
        die("Redirecting to index.php");
    }

    if(!session_id()) {
        session_start();
    }
    
    /*-----------------------Facebook Login--------------------------*/
    //get resources from fb php source
    require_once __DIR__ . '/src/Facebook/autoload.php';
    
    //setup application configuration
    $fb = new Facebook\Facebook([
                                'app_id' => '1532325777059788',
                                'app_secret' => 'ba7ae4f9ba79cde35c7d0f263a4422e6',
                                'default_graph_version' => 'v2.5',
                                ]);
    
    $helper = $fb->getRedirectLoginHelper();
    $permissions = ['email', 'user_likes']; // optional
    $loginUrl = $helper->getLoginUrl('http://addme.pagekite.me/fb-login.php', $permissions);
    
    //extract facebookurl from database if it is present
    $query = "
    SELECT
    facebookurl
    FROM users
    WHERE
    email = :email
    ";

    $query_params = array(    //look for a row with status='pending'. There should only be 1 row at anytime.
    ':email' => $_SESSION['user']['email']
    );

    try //execute the query
    {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
        die("Failed to run query: " . $ex->getMessage());
    }

    $row1 = $stmt->fetch(); //fetch the retrieved row
    $userdata['facebookurl'] = $row1['facebookurl'];
  

    /*-----------------------Instagram Login--------------------------*/
    //Make constants using define
    define('clientID', '6853f43243464ca9ad4bf9a1c2339495');
    define('clientSecret', '96583f205e7347bba2e1b7e7606fa1d2');
    define('redirectURI', 'http://addme.pagekite.me/account.php');
    define('ImageDirectory', 'pics/');
    
    if (isset($_GET['code'])) //isset checks for booleans
    {

        $code = ($_GET['code']);
        $url = 'https://api.instagram.com/oauth/access_token';
        $access_token_settings = array( 'client_id' => clientID,
                                        'client_secret' => clientSecret,
                                        'grant_type' => 'authorization_code',
                                        'redirect_uri' => redirectURI,
                                        'code' => $code
                                        );

        //cURL is what we use in PHP, its a library used to make calls to other apis.
        $curl = curl_init($url); //setting a curl session and we put in $url because thats where we are gettung the data from
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $access_token_settings); //setting the POSTFIELDS to the array setup that we created.
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //setting it to 1 means we are getting strings back
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $result = curl_exec($curl);
        curl_close($curl);

        $results = json_decode($result, true);
        $username = $results['user']['username'];
        $instagramurl = 'http://www.instagram.com/' . $username;

        //add url into database if login was successful
        $query = "UPDATE users
                SET
                instagramurl = :instagramurl
                WHERE
                email = :email
                ";

        // set query parameters
        $query_params = array(
                ':instagramurl' => $instagramurl,
                ':email' => $_SESSION['user']['email']
                );

        // execute query
        try
        {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex)
        {
            die("Failed to run query 2: " . $ex->getMessage());
        }

        //save instagramurl to session
        $_SESSION['user']['instagramurl']=$instagramurl;
    }

    //extract instagramurl from database if it is present
    $query = "
        SELECT
        instagramurl
        FROM users
        WHERE
        email = :email
        ";

    $query_params = array(    //look for a row with status='pending'. There should only be 1 row at anytime.
        ':email' => $_SESSION['user']['email']
        );

    try //execute the query
    {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
        die("Failed to run query: " . $ex->getMessage());
    }

    $row2 = $stmt->fetch(); //fetch the retrieved row
    $userdata['instagramurl'] = $row2['instagramurl'];

    /*-----------------------Linkedin Login--------------------------*/

    //extract linkedinurl from database
    $query = "
    SELECT
    linkedinurl
    FROM users
    WHERE
    email = :email
    ";

    $query_params = array(    //look for a row with status='pending'. There should only be 1 row at anytime.
    ':email' => $_SESSION['user']['email']
    );

    try //execute the query
    {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex)
    {
        die("Failed to run query: " . $ex->getMessage());
    }

    $row3 = $stmt->fetch(); //fetch the retrieved row
    $userdata['linkedinurl'] = $row3['linkedinurl'];

    /*-----------------------Diagnostics--------------------------
    print_r($userdata);

    if($userdata['facebookurl'])
    {
        echo 'done!';
    }
        if($userdata['instagramurl'])
    {
        echo 'done!';
    }
        if($userdata['linkedinurl'])
    {
        echo 'done!';
    }
    */


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

.material-icons.md-46 { font-size: 46px; }
.material-icons.green600 { color: #00c853; }
.material-icons.grey600 { color: #9e9e9e; }
.fa-facebook-official.grey600 { color: #9e9e9e; }

</style>

<title>Add.me</title>
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

<!-- page title and form for login submission -->
<div class="container">
    <h3 class="center-align">Link Profiles</h3>
    <h6 class="center-align">Login to connect your profiles to add.me</h6>
    <br>

    <div class="row">
        <div class="col s8 offset-s2">
            <a href="<?php echo $loginUrl;?>" class="btn blue darken-1 btn-large waves-light" style="width: 100%" name="action"><i class=" large fa fa-facebook-official"></i> Facebook</a>
        </div>
        <?php
        if($_SESSION['user']['facebookurl'])
        {
            echo "<i class=\"medium  material-icons md-46 green600\">done</i>";
        }
        else
        {
            echo "<i class=\"medium  material-icons md-46 grey600\">done</i>";
        }
        ?>
    </div>

    <div class="row">
        <div class="col s8 offset-s2">
            <a href="https:api.instagram.com/oauth/authorize/?client_id=<?php echo clientID; ?>&redirect_uri=<?php echo redirectURI; ?>&response_type=code" class="btn cyan darken-1 btn-large waves-light" style="width: 100%" name="action"><i class=" fa fa-instagram"></i> Instagram</a>
        </div>
        <?php
        if($_SESSION['user']['instagramurl'])
        {
            echo "<i class=\"medium  material-icons md-46 green600\">done</i>";
        }
        else
        {
            echo "<i class=\"medium  material-icons md-46 grey600\">done</i>";
        }
        ?>
    </div>

    <div class="row">
        <div class="col s8 offset-s2">
            <a href="linkedin-login.php" class="btn red accent-2 btn-large waves-light" style="width: 100%" name="action"><i class=" large fa fa-linkedin-square"></i> LinkedIn</a>
        </div>
        <?php
        if($_SESSION['user']['linkedinurl'])
        {
            echo "<i class=\"medium  material-icons md-46 green600\">done</i>";
        }
        else
        {
            echo "<i class=\"medium  material-icons md-46 grey600\">done</i>";
        }
        ?>
    </div>

    </br>

    <div class="row">
        <div class="col s8 offset-s2">
            <a href="home.php" class="btn teal darken-1 btn-large waves-light" style="width: 100%" name="action">Finish</a>
        </div>
    </div>

</div>


</body>
</html>

   