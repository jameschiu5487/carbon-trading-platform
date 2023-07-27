<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
            FROM fill_list
            INNER JOIN order_list
            ON order_list.user = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
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

    while ($row = mysqli_fetch_assoc($result2)) {

        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['time'] . '</td>';
        $html .= '<td>' . $row['request'] . '</td>';
        $html .= '<td>' . $row['volume'] . '</td>';
        $html .= '<td>' . $row['price'] . '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
?>
