<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $query = "SELECT * from order_list where username = '$user' ORDER BY id DESC";
    $result = sqlsrv_query($conn, $query);
    // 根據資料建立表格的HTML內容
    $html = '<table class="historical-data" id="historical">';
    $html .= '<tr>
                <th>ID</th>
                <th>Time</th>
                <th>Request</th>
                <th>Volume</th>
                <th>Price</th>
                <th>Current Status</th>
            </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        $html .= '<tr>';
        $html .= '<td>' . $row['id'] . '</td>';
        $html .= '<td>' . $row['time1'] . '</td>';
        $html .= '<td>' . $row['request'] . '</td>';
        $html .= '<td>' . $row['volume'] . '</td>';
        // $html .= '<td>' . $row['price'] . '</td>';
        
        $html .= '<td>';

        if ($row['price'] == NULL) {
            $html .= 'Market Price';
        } else {
            $html .= $row['price'];
        }
        $html .= '</td>';

        $html .= '<td>';

        if ($row['all_filled'] == 1) {
            $html .= '已成交';
        } else if ($row['filled'] == 0) {
            $html .= '未成交';
        } else {
            $html .= '部分成交 ' . $row['filled'] . ' / ' . $row['volume'];
        }

        $html .= '</td>';
        $html .= '</tr>';
    }
    $html .= '</table>';
    echo $html;
?>  