<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $sql_ask = "SELECT * FROM ask ORDER BY price ASC";
    $result_ask = sqlsrv_query($conn, $sql_ask);
    $ask_volumes_sum = 0;
    if ($result_ask) {
        while ($row = sqlsrv_fetch_array($result_ask, SQLSRV_FETCH_ASSOC)) {
            $ask_volumes[] = $row['volume'];
            $ask_prices[] = $row['price'];
        }
        for($i=0; $i<=4; $i++)
        {
            if(empty($ask_volumes[$i]))
            {
                $ask_volumes[$i] = 0;
                $ask_prices[$i] = 0;
            }
        }
    }

    for($i=4; $i>=0; $i--)
    {
        $ask_volumes_sum += $ask_volumes[$i];
    }
    for($i=4; $i>=0; $i--)
    {
        $ask_vol_per = ($ask_volumes[$i] / $ask_volumes_sum) * 100;
        $ask_vol_per = number_format($ask_vol_per, 2);
        if($ask_volumes[$i] == 0)
        {
    ?>
        <ul><?php echo "$".' '; ?></ul>
        <div class="progress-bar">
            <div class="ask_bar" style="width: <?php echo $ask_vol_per; ?>%;"></div>
        </div>
        <li><?php echo "Volume : ".' '; ?></li>
    <?php
        }
        else
        {
?>
    <ul><?php echo "$".$ask_prices[$i]; ?></ul>
    <div class="progress-bar">
        <div class="ask_bar" style="width: <?php echo $ask_vol_per; ?>%;"></div>
    </div>
    <li><?php echo "Volume : ".$ask_volumes[$i]; ?></li>
<?php
        }
    }
?>