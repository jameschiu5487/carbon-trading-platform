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
            ON order_list.user = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
            ORDER BY fill_list.filled_order_id DESC";
    $result2 = sqlsrv_query($conn, $sql);
    if ($result2) {
        if (mysqli_num_rows($result2)>0) {
            while ($row = mysqli_fetch_assoc($result2)) {
                $filled_volume[] = $row['volume'];
                $filled_request[] = $row['request'];
            }
            $count = count($filled_request);
        }
    }
    $sql = "SELECT usersEmission from users where usersName = '$user'";
    $result5 = sqlsrv_query($conn, $sql);
    if ($result5) {
        if (mysqli_num_rows($result5)>0) {
            while ($row = mysqli_fetch_assoc($result5)) {
                $emission = $row['usersEmission'];
            }
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
    $_SESSION["count"] = count($filled_request);
?>
<?php
    if($row = mysqli_fetch_assoc($result3))
    {
?>
    <p><?php echo "Credit : ".$row['usersEmission']; ?></p>
<?php
    }
?>
