<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $sql_bid = "SELECT * FROM bid ORDER BY price DESC";
    $result_bid = sqlsrv_query($conn, $sql_bid);
    $bid_volumes_sum = 0;
    if ($result_bid) {
        while ($row = sqlsrv_fetch_array($result_bid, SQLSRV_FETCH_ASSOC)) {
            $bid_volumes[] = $row['volume'];
            $bid_prices[] = $row['price'];
        }
    }
    for($i=0; $i<=4; $i++)
    {
        $bid_volumes_sum += $bid_volumes[$i];
    }
    for($i=0; $i<=4; $i++)
    {
        $bid_vol_per = ($bid_volumes[$i] / $bid_volumes_sum) * 200;
        $bid_vol_per = number_format($bid_vol_per, 2);
?>
    <ul><?php echo "$".$bid_prices[$i]; ?></ul>
    <div class="progress-bar">
        <div class="bid_bar" style="width: <?php echo $bid_vol_per; ?>%;"></div>
    </div>
    <li><?php echo "Volume : ".$bid_volumes[$i]; ?></li>
<?php
    }
?>