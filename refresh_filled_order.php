<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
            FROM fill_list
            INNER JOIN order_list
            ON order_list.username = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
            ORDER BY fill_list.filled_order_id DESC";
    $result2 = sqlsrv_query($conn, $sql);

    $html = '<table class="completed-trade-data" id="filled-data">';
    $html .= '<tr>
                <th>ID</th>
                <th>Time</th>
                <th>Request</th>
                <th>Volume</th>
                <th>Price</th>
            </tr>';

    while ($row = sqlsrv_fetch_array($result2, SQLSRV_FETCH_ASSOC)) {
        $dateTimeObject = $row['time']; // Assuming you have a DateTime object
        // $timestampString = (string) $dateTimeObject->getTimestamp();
        // Convert the DateTime object to a string using the format method  
        $formattedDate = $dateTimeObject->format('Y-m-d H:i:s');
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $formattedDate . '</td>';
        $html .= '<td>' . $row['request'] . '</td>';
        $html .= '<td>' . $row['volume'] . '</td>';
        $html .= '<td>' . $row['price'] . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
?>
