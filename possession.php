<?php 
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $query2 = "SELECT usersEmission FROM users where usersName = '$user' ";
    $result3 = sqlsrv_query($conn, $query2);

    $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
            FROM fill_list
            INNER JOIN order_list
            ON order_list.username = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
            ORDER BY fill_list.filled_order_id DESC";
    $result2 = sqlsrv_query($conn, $sql);
    $flag=0;
    if ($result2) {
        while ($row = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
            $filled_volume[] = $row['volume'];
            $filled_request[] = $row['request'];
            $flag=1;
        }
        if($flag==1){
            $count = count($filled_request);
        }
        else{
            $count = 0;
        }
    }
    
    $sql = "SELECT usersEmission from users where usersName = '$user'";
    $result5 = sqlsrv_query($conn, $sql);
    if ($result5) {
        while ($row = sqlsrv_fetch_array($result5, SQLSRV_FETCH_ASSOC)) {
            $emission = $row['usersEmission'];
            $flag_em = 1;
        } 
    }
    for($i=0; $i<($count-$_SESSION["count"]); $i++){
        if($filled_request[$i] == "buy"){
            $emission = $emission + $filled_volume[$i];   
        }
        else{
            $emission = $emission - $filled_volume[$i]; 
        }
        $sql = "UPDATE users set usersEmission = '$emission' where usersName = '$user'";
        sqlsrv_query($conn, $sql);
    }
    $_SESSION["count"] = $count;
?>
<?php
    if($row = sqlsrv_fetch_array($result3, SQLSRV_FETCH_ASSOC))
    {
?>
    <p><?php echo "Credit : ".$row['usersEmission']; ?></p>
<?php
    }
?>
