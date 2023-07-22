<?php

$serverName = "carbon-trading.database.windows.net";
$dBUsername = "IMFteam";
$dBPassword = "NYCUimf5487";
$dBName = "carbon-trading";

$conn = mysqli_connect($serverName, $dBUsername, $dBPassword, $dBName);

if(!$conn){
    die("Connection Failed: " . mysqli_connect_error());
}