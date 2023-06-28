<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $query1 = "SELECT price FROM fill_list ORDER BY filled_order_id DESC";
    $result1 = mysqli_query($conn, $query1);
    if($row = mysqli_fetch_assoc($result1)){
?>
    <p><?php echo "Market Price : $".$row['price']; ?></p>
<?php
    }
?>