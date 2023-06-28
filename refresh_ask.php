<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    $sql_ask = "SELECT * FROM ask ORDER BY price ASC";
    $result_ask = mysqli_query($conn, $sql_ask);
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
?>
    <ul><?php echo "$".$ask_prices[$i]; ?></ul>
    <li><?php echo "Volume : ".$ask_volumes[$i]; ?></li>
<?php
    }
?>