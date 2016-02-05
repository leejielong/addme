<?php

if(empty($_SESSION['user'])) //check if user is logged in
{
    header("Location: index.php"); //if user is not logged in, send user to main page
    die("Redirecting to index.php");
}

// include BarcodeQR class 
include "BarcodeQR.php"; 

// set BarcodeQR object 
$qr = new BarcodeQR(); 

// create URL QR code 
$qr->text($text); 

//save QR code image
$qr->draw(150, "qr-code.png");

// display new QR code image 
$qr->draw();



?>