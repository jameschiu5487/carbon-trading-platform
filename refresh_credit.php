<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
            FROM fill_list
            INNER JOIN order_list
            ON order_list.user = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
            ORDER BY fill_list.filled_order_id DESC";
    $result2 = sqlsrv_query($conn, $sql);
    if ($result2) {
        while ($row = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
            $filled_volume[] = $row['volume'];
            $filled_request[] = $row['request'];
        }
    }
    $sql = "SELECT usersEmission from users where usersName = '$user'";
    $result5 = sqlsrv_query($conn, $sql);
    if ($result5) {
        while ($row = sqlsrv_fetch_array($result5, SQLSRV_FETCH_ASSOC)) {
            $emission = $row['usersEmission'];
        }
    }
    if($filled_request[0] == "buy"){
        $emission = $emission + $filled_volume[0];   
    }
    else{
        $emission = $emission - $filled_volume[0]; 
    }
    $sql = "UPDATE users set usersEmission = '$emission' where usersName = '$user'";
    sqlsrv_query($conn, $sql);
?>
