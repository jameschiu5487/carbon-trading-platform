<?php
    session_start();
    require_once 'includes/dbh.inc.php';
    if(isset($_SESSION["useruid"])){
        $user = $_SESSION['useruid'];
    }
    $query = "SELECT * from order_list where user = '$user' ORDER BY id DESC";
    $result = mysqli_query($conn, $query);

    $query1 = "SELECT price FROM fill_list ORDER BY filled_order_id DESC";
    $result1 = mysqli_query($conn, $query1);

    $sql = "SELECT order_list.request, fill_list.time, order_list.id, fill_list.price, fill_list.volume
            FROM fill_list
            INNER JOIN order_list
            ON order_list.user = '$user' AND (fill_list.id1 = order_list.id OR fill_list.id2 = order_list.id)
            ORDER BY fill_list.filled_order_id DESC";
    $result2 = mysqli_query($conn, $sql);

    $sql_bid = "SELECT * FROM bid ORDER BY price DESC";
    $result_bid = mysqli_query($conn, $sql_bid);

    $sql_ask = "SELECT * FROM ask ORDER BY price ASC";
    $result_ask = mysqli_query($conn, $sql_ask); 

    $query2 = "SELECT usersEmission FROM users where usersName = '$user' ";
    $result3 = mysqli_query($conn, $query2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>carbon trading website</title>
    <link rel="stylesheet" href="css/trade.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            setInterval(function(){
                $.ajax({
                    url: "historical.php", // 修改成你的 PHP 檔案的路徑
                    success: function(data){
                        $("#historical").html(data); // 更新表格內容
                    }
                });
            }, 5000); // 每5秒更新一次資料
        });
    </script>
    <script>
        function refreshMarketPrice() {
            // AJAX 请求获取最新数据
            $.ajax({
                url: "refresh_market_price.php", // 根据实际情况修改请求的 URL
                success: function(data) {
                    // 更新市场价格
                    $("#market-price").html(data);
                }
            });
        }

        // 每隔 2 秒刷新一次市场价格
        setInterval(refreshMarketPrice, 1000);
    </script>
    <script>
        function refreshAsk() {
            // AJAX 请求获取最新数据
            $.ajax({
                url: "refresh_ask.php", // 根据实际情况修改请求的 URL
                success: function(data) {
                    // 更新市场价格
                    $("#ask-data").html(data);
                }
            });
        }

        // 每隔 2 秒刷新一次市场价格
        setInterval(refreshAsk, 2000);
    </script>
    <script>
        function refreshBid() {
            // AJAX 请求获取最新数据
            $.ajax({
                url: "refresh_bid.php", // 根据实际情况修改请求的 URL
                success: function(data) {
                    // 更新市场价格
                    $("#bid-data").html(data);
                }
            });
        }

        // 每隔 2 秒刷新一次市场价格
        setInterval(refreshBid, 2000);
    </script>
    <script>
        $(document).ready(function(){
            setInterval(function(){
                $.ajax({
                    url: "refresh_filled_order.php", // 修改成你的 PHP 檔案的路徑
                    success: function(data){
                        $("#filled-data").html(data); // 更新表格內容
                    }
                });
            }, 5000); // 每5秒更新一次資料
        });
    </script>
    <script>
        $(document).ready(function(){
            setInterval(function(){
                $.ajax({
                    url: "possession.php", // 修改成你的 PHP 檔案的路徑
                    success: function(data){
                        $("#possess").html(data); // 更新表格內容
                    }
                });
            }, 3000); // 每5秒更新一次資料
        });
    </script>
</head>
<body>   
    <main class="main-content">
        <section class="left-top">
            <div class="left-content">
                <div class="scrollable-window">
                    <div class="historical" id="historical">
                        <table class="historical-data" id = "historical">
                            <tr>
                                <th>ID</th>
                                <th>Time</th>
                                <th>Request</th>
                                <th>Volume</th>
                                <th>Price</th>
                                <th>Current Status</th>
                            </tr>
                            <tr>
                            <?php
                                    while($row = mysqli_fetch_assoc($result))
                                    {
                                ?>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['time1']; ?></td>
                                    <td><?php echo $row['request']; ?></td>
                                    <td><?php echo $row['volume']; ?></td>
                                    <td><?php if($row['price'] == NULL){echo "Market Price";} else{echo $row['price'];} ?></td>
                                    <td><?php if($row['all_filled'] == 1){echo "已成交";} else if($row['filled'] == 0){ echo "未成交";} else{ echo "部分成交 ".$row['filled']." / ".$row['volume'];} ?></td>
                                
                            </tr>
                                <?php
                                    }
                                ?> 
                        </table> 
                    </div>
                </div>   
            </div>      
        </section>
        <section class="left-bottom">
            <div class="scrollable-window">
                <table class="completed-trade-data" id="filled-data">
                    <tr>
                        <th>ID</th>
                        <th>Time</th>
                        <th>Request</th>
                        <th>Volume</th>
                        <th>Price</th>
                    </tr>
                    <tr>
                        <?php
                            while($row = mysqli_fetch_assoc($result2))
                            {
                        ?>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo $row['request']; ?></td>
                            <td><?php echo $row['volume']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            
                        
                    </tr>
                        <?php
                            }
                        ?>
                </table>
            </div>
        </section>
        <section class="right-top-left">
            <section class="order">
                <div class="possession" id="possess">
                    <?php
                        if($row = mysqli_fetch_assoc($result3))
                        {
                    ?>
                        <p><?php echo "Credit : ".$row['usersEmission']; ?></p>
                    <?php
                        }
                    ?>
                </div>
                <div class="order-title">
                    <h3>order type</h3>
                    <h3>Price</h3>
                    <h3>Volume</h3>
                </div>
            </section>
        </section>
        <section class="right-top-right">
            <div class="container">
                <nav>
                    <ul>
                        <?php
                            if(isset($_SESSION["useruid"])){
                                echo "<a href='includes/logout.inc.php'>Log out</a>";
                            }
                            else{
                                echo "<li><a href='login.php'>Login</a></li>";
                            }
                        ?>
                    </ul>
                </nav>
            </div>

            <section class="order-form">
                <form action="ordercheck.php" method="get">
                    <!-- <input type="text" name="instrument" placeholder="請輸入交易商品..."> -->
                    <select name="order_type">
                        <option value="limit order">Limit order</option>
                        <option value="market order">Market order</option>
                    </select>
                    <input type="number" name="price" min="1" oninput="validity.valid||(value='');" placeholder="請輸入欲交易的價格...">
                    <input type="number" name="volume" min="1" oninput="validity.valid||(value='');" placeholder="請輸入交易量...">
                    <div class="sell-bot">
                        <button type="submit" name="sell">Sell</button>
                    </div>
                    <div class="buy-bot">
                        <button type="submit" name="buy">Buy</button>
                    </div>
                </form>
            </section> 
        </section>
        <section class="right-bottom">
            <div class="market-order" id="refreshable-content">
                <h1 class="sell-title">ASK</h1>   
                    <div id="ask-data">
                        <?php
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
                    </div>
                    <div id="market-price">
                        <?php
                            if($row = mysqli_fetch_assoc($result1))
                            {
                        ?>
                            <p><?php echo "Market Price : $".$row['price']; ?></p>
                        <?php
                            }
                        ?>
                    </div>
                <h1 class="buy-title">BID</h1>
                    <div id="bid-data">
                        <?php
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
                    </div>

            </div>
        </section>  
    </main>
</body>


    