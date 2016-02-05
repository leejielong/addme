<?php

    //define connection information for MySQL database
    $username = "jl";
    $password = "ee3032";
    $host = "localhost";
    $dbname = "addme";

    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); //use utf8 charset for all fields

    try
    {
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options);//create PDO object
    }
    catch(PDOException $ex)
    {
        die("Failed to connect to the database: " . $ex->getMessage()); //show error if cannot connect to database
    }

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //configure PDO to throw exception upon errors

    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); //return rows from database using associative arrays

    header('Content-Type: text/html; charset=utf-8'); //configure browser to return content in UTF-8
    session_start(); //initialize session
