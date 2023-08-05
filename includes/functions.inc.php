<?php

// function emptyInputSignup($username, $emission, $industry, $pwd, $pwdrepeat){
//     $result;
//     if(empty($username) || empty($emission) || empty($industry) || empty($pwd) || empty($pwdrepeat)){
//         $result = true;
//     }
//     else{
//         $result = false;
//     }
//     return $result;
// }

// function invalidUid($username){
//     $result;
//     if(!preg_match("/^[a-zA-Z0-9]*$/", $username)){
//         $result = true;
//     }
//     else{
//         $result = false;
//     }
//     return $result;
// }

// function pwdMatch($pwd, $pwdrepeat){
//     $result;
//     if($pwd !== $pwdrepeat){
//         $result = true;
//     }
//     else{
//         $result = false;
//     }
//     return $result;
// }

// function createUser($conn, $username, $emission, $industry, $pwd){

//     $sql = "INSERT INTO users (usersName, usersEmission, usersIndustry, usersPwd) VALUES (?, ?, ?, ?)";
//     $stmt = mysqli_stmt_init($conn);
    
//     if(!mysqli_stmt_prepare($stmt, $sql)){
//         header("location: ../signup.php?error=stmtfailed");
//         exit();
//     }

//     $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

//     mysqli_stmt_bind_param($stmt, "ssss", $username, $emission, $industry, $hashedPwd);
//     mysqli_stmt_execute($stmt);
//     mysqli_stmt_close($stmt);
//     header("location: ../signup.php?error=none");
//     exit();
// }

// function emptyInputLogin($username, $pwd){
//     $result;
//     if(empty($username) || empty($pwd)){
//         $result = true;
//     }
//     else{
//         $result = false;
//     }
//     return $result;
// }

// function loginUser($conn, $username, $pwd){

//     $uidExists = uidExists($conn, $username, $username);

//     if($uidExists === false){
//         header("location: ../login.php?error=wronglogin");
//         exit();
//     }

//     $pwdHashed = $uidExists["usersPwd"];
//     $checkPwd = password_verify($pwd, $pwdHashed);

//     if($checkPwd === false){
//         header("location: ../login.php?error=wronglogin");
//     }
//     else if($checkPwd === true){
//         session_start();
//         $_SESSION["userid"] = $uidExists["usersID"];
//         $_SESSION["useruid"] = $uidExists["usersName"];
//         header("location: ../trade.php");
//         exit();
//     }
// }

// function uidExists($conn, $username){
//     $sql = "SELECT * FROM users WHERE usersName = ?;";
//     $stmt = mysqli_stmt_init($conn);
//     if(!mysqli_stmt_prepare($stmt, $sql)){
//         header("location: ../signup.php?error=stmtfailed");
//         exit();
//     }
//     mysqli_stmt_bind_param($stmt, "s", $username);
//     mysqli_stmt_execute($stmt);

//     $resultData = mysqli_stmt_get_result($stmt);
//     if($row = mysqli_fetch_assoc($resultData)){
//         return $row;
//     }
//     else{
//         $result = false;
//         return $result;
//     }
//     mysqli_stmt_close($stmt);
// }

// function corrPassword($conn, $username, $pwd) {
//     $sql = "SELECT * FROM users WHERE usersUid = ?;";
//     $stmt = mysqli_stmt_init($conn);
//     if (!mysqli_stmt_prepare($stmt, $sql)) {
//         header("location: ../login.php?error=stmtfailed");
//         exit();
//     }
//     mysqli_stmt_bind_param($stmt, "s", $username);
//     mysqli_stmt_execute($stmt);
//     $resultData = mysqli_stmt_get_result($stmt);
//     if ($row = mysqli_fetch_assoc($resultData)) {
//         $pwdHashed = $row['usersPwd'];
//         $checkPwd = password_verify($pwd, $pwdHashed);
//         if ($checkPwd === false) {
//             mysqli_stmt_close($stmt);
//             return false;
//         }
//         else if ($checkPwd === true) {
//             mysqli_stmt_close($stmt);
//             return true;
//         }
//     }
//     else {
//         mysqli_stmt_close($stmt);
//         return false;
//     }
// }

// function changePassword($conn, $username, $pwd) {

//     $sql = "UPDATE users SET usersPwd = ? WHERE usersUid = ?";
//     $stmt = mysqli_stmt_init($conn);

//     if(!mysqli_stmt_prepare($stmt, $sql)){
//         header("location: ../changepass.php?error=stmtfailed");
//         exit();
//     }

//     $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

//     mysqli_stmt_bind_param($stmt, "ss", $hashedPwd, $username);
//     mysqli_stmt_execute($stmt);
//     mysqli_stmt_close($stmt);
//     header("location: ../changepass.php?error=none");
//     exit();
// }

// function db_check(){
//     $servername = "localhost";
//     $username = "root";
//     $password = "";
//     $conn = new mysqli($servername, $username, $password);
//     if($conn->connect_error){
//         echo"error";
//         die("Connection Failed: " . mysqli_connect_error());
//     }
//     return $conn = new mysqli($servername, $username, $password, "carbon_trading_website");
// }





function emptyInputSignup($username, $emission, $industry, $pwd, $pwdrepeat){
    return empty($username) || empty($emission) || empty($industry) || empty($pwd) || empty($pwdrepeat);
}

function invalidUid($username){
    return !preg_match("/^[a-zA-Z0-9]*$/", $username);
}

function pwdMatch($pwd, $pwdrepeat){
    return $pwd !== $pwdrepeat;
}

function createUser($conn, $username, $emission, $industry, $pwd){

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (usersName, usersEmission, usersIndustry, usersPwd) VALUES ('$username', '$emission', '$industry', '$hashedPwd')";
    
    /*
    if (!$stmt){
        header("location: ../signup.php?error=stmtfailed");
        exit();
    }
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':emission', $emission);
    $stmt->bindParam(':industry', $industry);
    $stmt->bindParam(':hashedPwd', $hashedPwd);

    $stmt->execute();
    */
    sqlsrv_query($conn, $sql);
    header("location: ./signup.php?error=none");
    exit();
}

function emptyInputLogin($username, $pwd){
    return empty($username) || empty($pwd);
}

function loginUser($conn, $username, $pwd){
    echo "login user";
    $uidExists = uidExists($conn, $username);

    if(!$uidExists){
        header("location: ./login.php?error=wronglogin");
        exit();
    }
    print_r($uidExists);
    $pwdHashed = $uidExists[0]['usersPwd'];
    echo $pwdHashed;
    $checkPwd = password_verify($pwd, $pwdHashed);

    if(!$checkPwd){
        header("location: ./login.php?error=wronglogin");
    }
    else{
        session_start();
        $_SESSION["userid"] = $uidExists[0]["usersID"];
        $_SESSION["useruid"] = $uidExists[0]["usersName"];
        header("location: ./trade.php");
        exit();
    }
}

function uidExists($conn, $username){
    $sql = "SELECT * FROM users WHERE usersName = '$username'";
    #$stmt = $conn->prepare($sql);
    #$stmt->bindParam(':username', $username);
    #$stmt->execute();
    #$result = $stmt->fetch(PDO::FETCH_ASSOC);
    $result = sqlsrv_query($conn, $sql);
    if($result){
        $data = array();
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        $data[] = $row;
        }
    }
    echo "uidexists end";
    return $data ? $data : false;
}

function corrPassword($conn, $username, $pwd) {
    $sql = "SELECT * FROM users WHERE usersName = '$username'";
    $result = sqlsrv_query($conn, $sql);
    if ($result) {
        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
            $pwdHashed = $row['usersPwd'];
            return password_verify($pwd, $pwdHashed);
        } 
    }
    return false;
}

function changePassword($conn, $username, $pwd) {

    $sql = "UPDATE users SET usersPwd = '$pwd' WHERE usersName = '$username'";
    /*
    $stmt = $conn->prepare($sql);

    if(!$stmt){
        header("location: ../changepass.php?error=stmtfailed");
        exit();
    }

    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    $stmt->bindParam(':hashedPwd', $hashedPwd);
    $stmt->bindParam(':username', $username);

    $stmt->execute();
    */
    sqlsrv_query($conn,$sql);
    header("location: ../changepass.php?error=none");
    exit();
}

function db_check(){
    try {
        $conn = new PDO("sqlsrv:server = tcp:carbon-trading.database.windows.net,1433; Database = carbon-trading", "IMFteam", "NYCUimf5487");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        print("Error connecting to SQL Server.");
        die(print_r($e));
    }
    
    // SQL Server Extension Sample Code:
    $connectionInfo = array("UID" => "IMFteam", "pwd" => "NYCUimf5487", "Database" => "carbon-trading", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
    $serverName = "tcp:carbon-trading.database.windows.net,1433";
    $conn = sqlsrv_connect($serverName, $connectionInfo);
    return $conn;
}





