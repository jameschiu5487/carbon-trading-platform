<?php

if(isset($_POST["submit"])){

    $username = $_POST["name"];
    $pwd = $_POST["pwd"];
    $pwdnew = $_POST["pwdnew"];
    $pwdrepeat = $_POST["pwdrepeat"];

    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';

    if(emptyInputChangepass($username, $pwd, $pwdnew, $pwdrepeat) !== false){
        header("location: ../changepass.php?error=emptyinput");
        exit();
    }
    if(uidExists($conn, $username) === false){
        header("location: ../changepass.php?error=notauser");
        exit();
    }
    if(corrPassword($conn, $username, $pwd) === false){
        header("location: ../changepass.php?error=wrongoldpassword");
        exit();
    }
    if(pwdMatch($pwdnew, $pwdrepeat) !== false){
        header("location: ../changepass.php?error=passwordnotmatch");
        exit();
    }

    changePassword($conn, $username, $pwdnew);

}
else{
    header("location: ../changepass.php");
    exit();
}