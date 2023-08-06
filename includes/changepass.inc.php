<?php
ob_start(); // Enable output buffering
if(isset($_POST["submit"])){

    $username = $_POST["name"];
    $pwd = $_POST["pwd"];
    $pwdnew = $_POST["pwdnew"];
    $pwdrepeat = $_POST["pwdrepeat"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if(emptyInputChangepass($username, $pwd, $pwdnew, $pwdrepeat) !== false){
        header("location: ../changepass.php?error=emptyinput");
        ob_end_flush();
    }
    if(uidExists($conn, $username) === false){
        header("location: ../changepass.php?error=notauser");
        ob_end_flush();
    }
    if(corrPassword($conn, $username, $pwd) === false){
        header("location: ../changepass.php?error=wrongoldpassword");
        ob_end_flush();
    }
    if(pwdMatch($pwdnew, $pwdrepeat) !== false){
        header("location: ../changepass.php?error=passwordnotmatch");
        ob_end_flush();
    }

    changePassword($conn, $username, $pwdnew);

}
else{
    header("location: ../changepass.php");
    exit();
}