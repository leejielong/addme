<?php

    unset($_SESSION['user']); //remove user data from the session

    header("Location: index.php"); //redirect user to main page
    die("Redirecting to: index.php");
