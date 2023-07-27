<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $sql_ask = "SELECT * FROM ask ORDER BY price ASC";
    $result_ask = sqlsrv_query($conn, $sql_ask);
    $ask_volumes_sum = 0;
    if ($result_ask) {
        if (mysqli_num_rows($result_ask)>0) {
            while ($row = mysqli_fetch_assoc($result_ask)) {
                $ask_volumes[] = $row['volume'];
                $ask_prices[] = $row['price'];
            }
        }
    }
    for($i=4; $i>=0; $i--)
    {
        $ask_volumes_sum += $ask_volumes[$i];
    }
    for($i=4; $i>=0; $i--)
    {
        $ask_vol_per = ($ask_volumes[$i] / $ask_volumes_sum) * 200;
        $ask_vol_per = number_format($ask_vol_per, 2);
?>
    <ul><?php echo "$".$ask_prices[$i]; ?></ul>
    <div class="progress-bar">
        <div class="ask_bar" style="width: <?php echo $ask_vol_per; ?>%;"></div>
    </div>
    <li><?php echo "Volume : ".$ask_volumes[$i]; ?></li>
<?php
    }
?>