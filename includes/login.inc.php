<?php
    session_start();
    require_once 'dbh.inc.php';
    require_once 'functions.inc.php';
    if(isset($_POST["submit"])){
        $username = $_POST["name"];
        $pwd = $_POST["pwd"];
        $_SESSION["useruid"] = $username;
        if(isset($_SESSION["useruid"])){
            $user = $_SESSION['useruid'];
        }
        $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
                FROM fill_list
                INNER JOIN order_list
                ON order_list.user = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
                ORDER BY fill_list.filled_order_id DESC";
        $result2 = mysqli_query($conn, $sql);
        print_r($result2);
        if (mysqli_num_rows($result2)>0) {
            echo  "!!!";
            while ($row = mysqli_fetch_assoc($result2)) {
                $filled_request[] = $row['request'];
            }
            $count = count($filled_request);
            $_SESSION["count"] = count($filled_request);
        }
        else {
            $_SESSION["count"] = 0;
        }            

        if(emptyInputLogin($username, $pwd) !== false){
            header("location: ../login.php?error=emptyinput");
            exit();
        }

        loginUser($conn, $username, $pwd);
        // setcookie("user", $username, time() + 86400);        
    }
    else{
        header("location: ../login.php");
        exit();
    }