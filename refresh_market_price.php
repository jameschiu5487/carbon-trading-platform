<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $query1 = "SELECT price FROM fill_list ORDER BY filled_order_id DESC";
    $result1 = sqlsrv_query($conn, $query1);
    if($row = sqlsrv_fetch_array($result1, SQLSRV_FETCH_ASSOC)){
?>
    <p><?php echo "Market Price : $".$row['price']; ?></p>
<?php
    }
?>