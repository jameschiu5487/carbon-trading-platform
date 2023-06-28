<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $sql_bid = "SELECT * FROM bid ORDER BY price DESC";
    $result_bid = mysqli_query($conn, $sql_bid);
    if ($result_bid) {
        if (mysqli_num_rows($result_bid)>0) {
            while ($row = mysqli_fetch_assoc($result_bid)) {
                $bid_volumes[] = $row['volume'];
                $bid_prices[] = $row['price'];
            }
        }
    }
    for($i=0; $i<=4; $i++)
    {
?>
    <ul><?php echo "$".$bid_prices[$i]; ?></ul>
    <li><?php echo "Volume : ".$bid_volumes[$i]; ?></li>
<?php
    }
?>