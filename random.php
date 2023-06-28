<?php
require_once 'includes/dbh.inc.php';
// require_once 'includes/functions.inc.php';
require_once 'ordercheck.php';

session_start();
if (isset($_GET['start'])){
    echo "connect";
    for($i=1; $i<=200; $i++){
        if (isset($_GET['stop'])){
            echo "stop";
            break;
        }
        else {
            echo "random";
            random_order();
            echo " random_complete";
            sleep(1);
        }
    }
}

function random_order(){
    $conn = db_check();
    $array_ask = Get_Ask_Data($conn);
    $ask_prices = $array_ask[0];
    $ask_volumes = $array_ask[1];
    $array_bid = Get_Bid_Data($conn);
    $bid_prices = $array_bid[0];
    $bid_volumes = $array_bid[1];
    $query = [
        'request' => rand(0,1),
        'price' => rand($bid_prices[0]-5, $bid_prices[0]+5),
        'volume' => rand(10,50),
        'order_type' => rand(0,4),
        'user' => "com"
    ];
    echo $query["request"];
    echo $query["order_type"];
    #echo $query["order_type"];
    if ($query["request"]==1){
        $query["request"] = "buy";
        if ($query["order_type"]!=0){
            $query["order_type"] = "limit order";
            Limit_order_buy($query['request'], $query['price'], $query['volume'], $query['user'], $conn);
        }
        else {
            $query["order_type"] = "IOC order";
            IOC_order_buy($query['request'], $query['volume'], $query['user'], $conn);
        }
        
    }
    else {
        $query["request"] = "sell";
        if ($query["order_type"]!=0){
            $query["order_type"] = "limit order";
            Limit_order_sell($query['request'], $query['price'], $query['volume'], $query['user'], $conn);
        }
        else {
            $query["order_type"] = "IOC order";
            IOC_order_sell($query['request'], $query['volume'], $query['user'], $conn);
        }
    }
}
$conn->close();
header("Location: random.php");
