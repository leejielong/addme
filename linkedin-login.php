<?php
/*
 * login_with_linkedin.php
 *
 * @(#) $Id: login_with_linkedin.php,v 1.5 2015/07/27 19:27:07 mlemos Exp $
 *
 */

	/*
	 *  Get the http.php file from http://www.phpclasses.org/httpclient
	 */

    require("common.php"); //connect to database

    if(empty($_SESSION['user'])) //check if user is logged in
    {
        header("Location: index.php"); //if user is not logged in, send user to main page
        die("Redirecting to index.php");
    }

    if(!session_id()) {
        session_start();
    }

	require('http.php');
	require('oauth_client.php');

	$client = new oauth_client_class;
	$client->debug = false;
	$client->debug_http = true;
	$client->server = 'LinkedIn';
	$client->redirect_uri = 'http://'.$_SERVER['HTTP_HOST'].
		dirname(strtok($_SERVER['REQUEST_URI'],'?')).'/linkedin-login.php';

	/*
	 * Uncomment the next line if you want to use
	 * the pin based authorization flow
	 */
	// $client->redirect_uri = 'oob';

	/*
	 * Was this script included defining the pin the
	 * user entered to authorize the API access?
	 */
	if(defined('OAUTH_PIN'))
		$client->pin = OAUTH_PIN;

	$client->client_id = '77u41dfd7dtlda'; 
	$application_line = __LINE__;
	$client->client_secret = 'DZW6SggvrlJgE9Zr';

	/*  API permission scopes
	 *  Separate scopes with a space, not with +
	 */
	$client->scope = 'r_basicprofile';

	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://api.linkedin.com/v1/people/~:(public-profile-url)', 
					'GET', array(
						'format'=>'json'
					), array('FailOnAccessError'=>true), $user);

			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
		exit;
	if(strlen($client->authorization_error))
	{
		$client->error = $client->authorization_error;
		$success = false;
	}

	$linkedinurl = $user->publicProfileUrl;

	//add linkedinurl to database
	$query = "UPDATE users
                SET
                linkedinurl = :linkedinurl
                WHERE
                email = :email
                ";

    //set query parameters
    $query_params = array(
    			':linkedinurl' => $linkedinurl,
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

	//save linkedinurl to session
    $_SESSION['user']['linkedinurl']=$linkedinurl;
    

    //return to account setup page
    header("Location: account.php");
    die("Redirecting to account.php");

?>