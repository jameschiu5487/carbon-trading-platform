<?php
ob_start(); // Enable output buffering
if(isset($_POST["submit"])){

    $username = $_POST["name"];
    $emission = $_POST["emission"]*0.8;
    $industry = $_POST["industry"];
    $pwd = $_POST["pwd"];
    $pwdrepeat = $_POST["pwdrepeat"];
    echo $username;
    echo $emission;
    echo $industry;
    echo $pwd;
    echo $pwdrepeat;

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

     if(emptyInputSignup($username, $emission, $industry, $pwd, $pwdrepeat) !== false){
         header("location: ../signup.php?error=emptyinput");
         exit();
     }    
     if(invalidUid($username) !== false){
         header("location: ../signup.php?error=invaliduid");
         exit();
     }    
     if(pwdMatch($pwd, $pwdrepeat) !== false){
         header("location: ../signup.php?error=passwordnotmatch");
         exit();
     } 
     if(uidExists($conn, $username) !== false){
         header("location: ../signup.php?error=usernametaken");
         exit();
    }   

    createUser($conn, $username, $emission, $industry, $pwd);
}
else{
    // header("location: ../signup.php");
    // exit();
}
