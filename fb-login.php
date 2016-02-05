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
    
    //get resources from fb php source
    require_once __DIR__ . '/src/Facebook/autoload.php';
    
    //setup application configuration
    $fb = new Facebook\Facebook([
                                'app_id' => '1532325777059788',
                                'app_secret' => 'ba7ae4f9ba79cde35c7d0f263a4422e6',
                                'default_graph_version' => 'v2.5',
                                ]);
    
    $helper = $fb->getRedirectLoginHelper();
    
    //get access token
    try {
        $accessToken = $helper->getAccessToken();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }
        
    if (isset($accessToken)) //if there access token is available
    {
        if (isset($_SESSION['facebook_access_token'])) //check whether session already has a token
        {
            // Sets the default fallback access token so we don't have to pass it to each request
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
        else
        {
            // getting short-lived access token
            $_SESSION['facebook_access_token'] = (string) $accessToken;
            // OAuth 2.0 client handler
            $oAuth2Client = $fb->getOAuth2Client();
            // Exchanges a short-lived access token for a long-lived one
            $longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
            //save long token into session
            $_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
            // setting default access token to be used in script
            $fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
        }
    }
    
    try {
        $response = $fb->get('/me?fields=id,name,link');
        $userNode = $response->getGraphUser();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
        // When Graph returns an error
        echo 'Graph returned an error: ' . $e->getMessage();
        exit;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
        // When validation fails or other local issues
        echo 'Facebook SDK returned an error: ' . $e->getMessage();
        exit;
    }

    //insert fb link into database
    $facebookurl = 'http://www.facebook.com/' . $userNode['id'];

    $query = "UPDATE users
                SET
                facebookurl = :facebookurl
                WHERE
                email = :email
                ";

    // set query parameters
    $query_params = array(
                ':facebookurl' => $facebookurl,
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

    //save facebookurl to session
    $_SESSION['user']['facebookurl']=$facebookurl;

    print_r($userNode);
    //return to account setup page
    header("Location: account.php");
    die("Redirecting to account.php");

?>
    
    
    
    