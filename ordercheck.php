<?php
require_once 'includes/dbh.inc.php';
require_once 'includes/functions.inc.php';

session_start();
if(isset($_SESSION["useruid"])){
    $useruid = $_SESSION['useruid'];
}
if (isset($_GET['buy'])){
  $query = [
    'request' => "buy",
    'price' => htmlspecialchars($_GET["price"]),
    'volume' => htmlspecialchars($_GET["volume"]),
    'order_type' => htmlspecialchars($_GET["order_type"]),
    'user' => $useruid
  ];
  $conn = db_check();
  #echo $query["order_type"];
  if ($query["order_type"]=="limit order"){
    Limit_order_buy($query['request'], $query['price'], $query['volume'], $query['user'], $conn);
  }
  else {
    IOC_order_buy($query['request'], $query['volume'], $query['user'], $conn);
  }
}
if (isset($_GET['sell'])){
  $query = [
    'request' => "sell",
    'price' => htmlspecialchars($_GET["price"]),
    'volume' => htmlspecialchars($_GET["volume"]),
    'order_type' => htmlspecialchars($_GET["order_type"]),
    'user' => $useruid
  ];
  $conn = db_check();
  #echo $query["order_type"];
  if ($query["order_type"]=="limit order"){
    Limit_order_sell($query['request'], $query['price'], $query['volume'], $query['user'], $conn);
  }
  else {
    IOC_order_sell($query['request'], $query['volume'], $query['user'], $conn);
  }
}

function Limit_order_buy($request, $price, $volume, $user, $conn) {
  $sql = "INSERT INTO order_list (request, price, volume, user, filled, unfilled, all_filled)VALUES ('$request', '$price', '$volume', '$user', 0, '$volume', 0)";
  mysqli_query($conn, $sql);
  $sql = "SELECT id FROM order_list ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $id = $row['id'];
        }
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  //拿ask data
  $array_ask = Get_Ask_Data($conn);
  $flag_ask = $array_ask[2];
  $ask_prices = $array_ask[0];
  $ask_volumes = $array_ask[1];
  print_r($ask_prices);
  echo "<br>";
  //print_r($ask_volumes);
  //拿bid data
  $array_bid = Get_Bid_Data($conn);
  $flag_bid = $array_bid[2];
  $bid_prices = $array_bid[0];
  $bid_volumes = $array_bid[1];
  //print_r($bid_prices);
  //print_r($bid_volumes);
  $market_price = ($ask_prices[0]+$bid_prices[0])/2;
  if($flag_bid!=0 && $flag_ask!=0){
    //判斷limit是否大於市價
    if ($price>=$ask_prices[0]){
      $count = $volume;
      for ($i=0; $ask_prices[$i]<=$price ;$i++){
        echo $ask_prices[$i];
        $ask_price = $ask_prices[$i];
        echo "<br>";
        $sql = "SELECT time1, id, unfilled, volume FROM order_list WHERE (price = '$ask_price' and all_filled = 0)and request = 'sell'";
        $result = mysqli_query($conn, $sql);
        unset($ids);
        unset($unfilled);
        unset($volumes);
        if ($result) {
          // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
          if (mysqli_num_rows($result)>0) {
              // 取得大於0代表有資料
              // while迴圈會根據資料數量，決定跑的次數
              // mysqli_fetch_assoc方法可取得一筆值
              while ($row = mysqli_fetch_assoc($result)) {
                  // 每跑一次迴圈就抓一筆值，最後放進data陣列中
                  $time[] = $row['time1'];
                  $ids[] = $row['id'];
                  $unfilled[] = $row['unfilled'];
                  $volumes[] = $row['volume'];
              }
          }
          // 釋放資料庫查到的記憶體
          mysqli_free_result($result);
        }
        print_r($time);
        echo "<br>";
        print_r($ids);
        echo "<br>";
        print_r($unfilled);
        echo "<br>";
        print_r($volumes);
        echo "<br>";
        for ($j=0; $j<count($ids) ;$j++){
          echo $count;
          //echo $unfilled[$i];
          if($count >= $unfilled[$j]){
            $count = $count-$unfilled[$j];
            $sql_update_order_list = "UPDATE order_list SET filled = '$volumes[$j]', unfilled = 0, all_filled = 1 WHERE id = '$ids[$j]'";
            mysqli_query($conn, $sql_update_order_list);
            $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$ask_prices[$i]', '$unfilled[$j]')";
            mysqli_query($conn, $sql_update_fill_list);
            //成交改ask的量
            echo " ";
            echo "volume change1: ";
            echo -$unfilled[$j];
            echo "<br>";
            Edit_Ask_Volume($ask_prices[$i], -$unfilled[$j],$conn);
            //echo $count;
          } 
          else if ($count < $unfilled[$j]){
            $l_count = $unfilled[$j]-$count;
            $filled = $volumes[$j]-$l_count;
            $sql_update_order_list = "UPDATE order_list SET filled = '$filled', unfilled = '$l_count' WHERE id = '$ids[$j]'";
            mysqli_query($conn, $sql_update_order_list);
            $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$ask_prices[$i]', '$count')";
            mysqli_query($conn, $sql_update_fill_list);
            //成交改ask的量
            echo " ";
            echo "volume change2: ";
            echo -$count;
            echo "<br>";
            Edit_Ask_Volume($ask_prices[$i], -$count,$conn);
            $count = 0;
          }
          if ($count==0){
            break;
          }
        }
        if ($count==0){
          $sql_update_order_list = "UPDATE order_list SET filled = '$volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
          mysqli_query($conn, $sql_update_order_list);
          break;
        }
      }
      echo "end";
    }
    //判斷在bid是否有相同價格
    else{
      if (in_array($price, $bid_prices)){
        echo "in bid and have same price";
        Edit_Bid_Volume($price, $volume, $conn);
      }
      else {
        echo "in bid but no same price";
        $sql = "INSERT INTO bid VALUES ('{$price}', '{$volume}')";
        mysqli_query($conn, $sql);
      }
    }
  }
  else if ($flag_bid!=0){
    echo "nothing in bid or ask";
    if (in_array($price, $bid_prices)){
      echo "in bid and have same price";
      Edit_Bid_Volume($price, $volume, $conn);
    }
    else {
      echo "in bid but no same price";
      $sql = "INSERT INTO bid VALUES ('{$price}', '{$volume}')";
      mysqli_query($conn, $sql);
    }
  }
  else {
    echo "nothing in bid or ask";
    echo "in bid but no same price";
    $sql = "INSERT INTO bid VALUES ('{$price}', '{$volume}')";
    mysqli_query($conn, $sql);
  }     
}
function IOC_order_buy($request, $volume, $user, $conn){
  $sql = "INSERT INTO order_list (request, price, volume, user, filled, unfilled, all_filled)VALUES ('$request', NULL, '$volume', '$user', 0, '$volume', 0)";
  mysqli_query($conn, $sql);
  $sql = "SELECT id FROM order_list ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $id = $row['id'];
        }
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  //拿ask data
  $array_ask = Get_Ask_Data($conn);
  $flag_ask = $array_ask[2];
  $ask_prices = $array_ask[0];
  $ask_volumes = $array_ask[1];
  print_r($ask_prices);
  echo "<br>";
  //print_r($ask_volumes);
  //拿bid data
  $array_bid = Get_Bid_Data($conn);
  $flag_bid = $array_bid[2];
  $bid_prices = $array_bid[0];
  $bid_volumes = $array_bid[1];
  //print_r($bid_prices);
  //print_r($bid_volumes);
  $market_price = ($ask_prices[0]+$bid_prices[0])/2;
  if($flag_ask!=0){
    $count = $volume;
    $total_volume = 0;
    for ($i=0; $i<count($ask_prices) ;$i++){
      echo $ask_prices[$i];
      $ask_price = $ask_prices[$i];
      echo "<br>";
      $sql = "SELECT time1, id, unfilled, volume FROM order_list WHERE (price = '$ask_price' and all_filled = 0)and request = 'sell'";
      $result = mysqli_query($conn, $sql);
      unset($ids);
      unset($unfilled);
      unset($volumes);
      if ($result) {
        // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
        if (mysqli_num_rows($result)>0) {
            // 取得大於0代表有資料
            // while迴圈會根據資料數量，決定跑的次數
            // mysqli_fetch_assoc方法可取得一筆值
            while ($row = mysqli_fetch_assoc($result)) {
                // 每跑一次迴圈就抓一筆值，最後放進data陣列中
                $time = $row['time1'];
                $ids[] = $row['id'];
                $unfilled[] = $row['unfilled'];
                $volumes[] = $row['volume'];
            }
        }
        // 釋放資料庫查到的記憶體
        mysqli_free_result($result);
      }
      print_r($time);
      echo "<br>";
      print_r($ids);
      echo "<br>";
      print_r($unfilled);
      echo "<br>";
      print_r($volumes);
      echo "<br>";
      for ($j=0; $j<count($ids) ;$j++){
        echo $count;
        //echo $unfilled[$i];
        if($count >= $unfilled[$j]){
          $count = $count-$unfilled[$j];
          $sql_update_order_list = "UPDATE order_list SET filled = '$volumes[$j]', unfilled = 0, all_filled = 1 WHERE id = '$ids[$j]'";
          mysqli_query($conn, $sql_update_order_list);
          $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$ask_prices[$i]', '$unfilled[$j]')";
          mysqli_query($conn, $sql_update_fill_list);
          $total_volume+=$unfilled[$j];
          //成交改ask的量
          echo " ";
          echo "volume change1: ";
          echo -$unfilled[$j];
          echo "<br>";
          Edit_Ask_Volume($ask_prices[$i], -$unfilled[$j],$conn);
          //echo $count;
        } 
        else if ($count < $unfilled[$j]){
          $l_count = $unfilled[$j]-$count;
          $filled = $volumes[$j]-$l_count;
          $sql_update_order_list = "UPDATE order_list SET filled = '$filled', unfilled = '$l_count' WHERE id = '$ids[$j]'";
          mysqli_query($conn, $sql_update_order_list);
          $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$ask_prices[$i]', '$count')";
          mysqli_query($conn, $sql_update_fill_list);
          $total_volume+=$count;
          //成交改ask的量
          echo " ";
          echo "volume change2: ";
          echo -$count;
          echo "<br>";
          Edit_Ask_Volume($ask_prices[$i], -$count,$conn);
          $count = 0;
        }
        if ($count==0){
          break;
        }
      }
      if ($count==0){
        $sql_update_order_list = "UPDATE order_list SET filled = '$volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
        mysqli_query($conn, $sql_update_order_list);
        break;
      }
    }
    if($count != 0){
      $sql_update_order_list = "UPDATE order_list SET filled = '$total_volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
      mysqli_query($conn, $sql_update_order_list);
    }
    echo "end";
  }
  else{
    echo "nothing in bid or ask";
    $sql_update_order_list = "DELETE FROM order_list WHERE id = '$id'";
    mysqli_query($conn, $sql_update_order_list);
  }
}
function Limit_order_sell($request, $price, $volume, $user, $conn) {
  $sql = "INSERT INTO order_list (request, price, volume, user, filled, unfilled, all_filled)VALUES ('$request', '$price', '$volume', '$user', 0, '$volume', 0)";
  mysqli_query($conn, $sql);
  $sql = "SELECT id FROM order_list ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $id = $row['id'];
        }
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  //拿ask data
  $array_ask = Get_Ask_Data($conn);
  $flag_ask = $array_ask[2];
  $ask_prices = $array_ask[0];
  $ask_volumes = $array_ask[1];
  print_r($ask_prices);
  echo "<br>";
  //print_r($ask_volumes);
  //拿bid data
  $array_bid = Get_Bid_Data($conn);
  $flag_bid = $array_bid[2];
  $bid_prices = $array_bid[0];
  $bid_volumes = $array_bid[1];
  //print_r($bid_prices);
  //print_r($bid_volumes);
  $market_price = ($ask_prices[0]+$bid_prices[0])/2;
  if($flag_bid!=0 && $flag_ask!=0){
    //判斷limit是否小於市價
    if ($price<=$bid_prices[0]){
      $count = $volume;
      for ($i=0; $bid_prices[$i]<=$price ;$i++){
        echo $bid_prices[$i];
        $bid_price = $bid_prices[$i];
        echo "<br>";
        $sql = "SELECT time1, id, unfilled, volume FROM order_list WHERE (price = '$bid_price' and all_filled = 0)and request = 'buy'";
        $result = mysqli_query($conn, $sql);
        unset($ids);
        unset($unfilled);
        unset($volumes);
        if ($result) {
          // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
          if (mysqli_num_rows($result)>0) {
              // 取得大於0代表有資料
              // while迴圈會根據資料數量，決定跑的次數
              // mysqli_fetch_assoc方法可取得一筆值
              while ($row = mysqli_fetch_assoc($result)) {
                  // 每跑一次迴圈就抓一筆值，最後放進data陣列中
                  $time = $row['time1'];
                  $ids[] = $row['id'];
                  $unfilled[] = $row['unfilled'];
                  $volumes[] = $row['volume'];
              }
          }
          // 釋放資料庫查到的記憶體
          mysqli_free_result($result);
        }
        print_r($time);
        echo "<br>";
        print_r($ids);
        echo "<br>";
        print_r($unfilled);
        echo "<br>";
        print_r($volumes);
        echo "<br>";
        for ($j=0; $j<count($ids) ;$j++){
          echo $count;
          //echo $unfilled[$i];
          if($count >= $unfilled[$j]){
            $count = $count-$unfilled[$j];
            $sql_update_order_list = "UPDATE order_list SET filled = '$volumes[$j]', unfilled = 0, all_filled = 1 WHERE id = '$ids[$j]'";
            mysqli_query($conn, $sql_update_order_list);
            $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$bid_prices[$i]', '$unfilled[$j]')";
            mysqli_query($conn, $sql_update_fill_list);
            //成交改bid的量
            echo " ";
            echo "volume change1: ";
            echo -$unfilled[$j];
            echo "<br>";
            Edit_Bid_Volume($bid_prices[$i], -$unfilled[$j],$conn);
            //echo $count;
          } 
          else if ($count < $unfilled[$j]){
            $l_count = $unfilled[$j]-$count;
            $filled = $volumes[$j]-$l_count;
            $sql_update_order_list = "UPDATE order_list SET filled = '$filled', unfilled = '$l_count' WHERE id = '$ids[$j]'";
            mysqli_query($conn, $sql_update_order_list);
            $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$bid_prices[$i]', '$count')";
            mysqli_query($conn, $sql_update_fill_list);
            //成交改bid的量
            echo " ";
            echo "volume change2: ";
            echo -$count;
            echo "<br>";
            Edit_Bid_Volume($bid_prices[$i], -$count,$conn);
            $count = 0;
          }
          if ($count==0){
            break;
          }
        }
        if ($count==0){
          $sql_update_order_list = "UPDATE order_list SET filled = '$volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
          mysqli_query($conn, $sql_update_order_list);
          break;
        }
      }
      echo "end";
    }
    //判斷在ask是否有相同價格
    else{
      if (in_array($price, $ask_prices)){
        echo "in ask and have same price";
        Edit_ASK_Volume($price, $volume, $conn);
      }
      else {
        echo "in ask but no same price";
        $sql = "INSERT INTO ask VALUES ('{$price}', '{$volume}')";
        mysqli_query($conn, $sql);
      }
    }

  }   
  else if ($flag_ask!=0){
    echo "nothing in bid or ask";
    if (in_array($price, $ask_prices)){
      echo "in ask and have same price";
      Edit_ASK_Volume($price, $volume, $conn);
    }
    else {
      echo "in ask but no same price";
      $sql = "INSERT INTO ask VALUES ('{$price}', '{$volume}')";
      mysqli_query($conn, $sql);
    }
  }    
  else {
    echo "nothing in bid or ask";
    echo "in ask but no same price";
    $sql = "INSERT INTO ask VALUES ('{$price}', '{$volume}')";
    mysqli_query($conn, $sql);
  }
}
function IOC_order_sell($request, $volume, $user, $conn){
  $sql = "INSERT INTO order_list (request, price, volume, user, filled, unfilled, all_filled)VALUES ('$request', NULL, '$volume', '$user', 0, '$volume', 0)";
  mysqli_query($conn, $sql);
  $sql = "SELECT id FROM order_list ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $id = $row['id'];
        }
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  //拿ask data
  $array_ask = Get_Ask_Data($conn);
  $flag_ask = $array_ask[2];
  $ask_prices = $array_ask[0];
  $ask_volumes = $array_ask[1];
  print_r($ask_prices);
  echo "<br>";
  //print_r($ask_volumes);
  //拿bid data
  $array_bid = Get_Bid_Data($conn);
  $flag_bid = $array_bid[2];
  $bid_prices = $array_bid[0];
  $bid_volumes = $array_bid[1];
  //print_r($bid_prices);
  //print_r($bid_volumes);
  $market_price = ($ask_prices[0]+$bid_prices[0])/2;
  if($flag_bid!=0){
    //判斷limit是否小於市價
    $count = $volume;
    $total_volume = 0;
    for ($i=0; $i<count($bid_prices) ;$i++){
      echo $bid_prices[$i];
      $bid_price = $bid_prices[$i];
      echo "<br>";
      $sql = "SELECT time1, id, unfilled, volume FROM order_list WHERE (price = '$bid_price' and all_filled = 0)and request = 'buy'";
      $result = mysqli_query($conn, $sql);
      unset($ids);
      unset($unfilled);
      unset($volumes);
      if ($result) {
      // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
        if (mysqli_num_rows($result)>0) {
          // 取得大於0代表有資料
          // while迴圈會根據資料數量，決定跑的次數
          // mysqli_fetch_assoc方法可取得一筆值
          while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $time = $row['time1'];
            $ids[] = $row['id'];
            $unfilled[] = $row['unfilled'];
            $volumes[] = $row['volume'];
              }
          }
          // 釋放資料庫查到的記憶體
        mysqli_free_result($result);
      }
      print_r($time);
      echo "<br>";
      print_r($ids);
      echo "<br>";
      print_r($unfilled);
      echo "<br>";
      print_r($volumes);
      echo "<br>";
      for ($j=0; $j<count($ids) ;$j++){
        echo $count;
        //echo $unfilled[$i];
        if($count >= $unfilled[$j]){
          $count = $count-$unfilled[$j];
          $sql_update_order_list = "UPDATE order_list SET filled = '$volumes[$j]', unfilled = 0, all_filled = 1 WHERE id = '$ids[$j]'";
          mysqli_query($conn, $sql_update_order_list);
          $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$bid_prices[$i]', '$unfilled[$j]')";
          mysqli_query($conn, $sql_update_fill_list);
          $total_volume+=$unfilled[$j];
          //成交改bid的量
          echo " ";
          echo "volume change1: ";
          echo -$unfilled[$j];
          echo "<br>";
          Edit_Bid_Volume($bid_prices[$i], -$unfilled[$j],$conn);
            //echo $count;
        } 
        else if ($count < $unfilled[$j]){
          $l_count = $unfilled[$j]-$count;
          $filled = $volumes[$j]-$l_count;
          $sql_update_order_list = "UPDATE order_list SET filled = '$filled', unfilled = '$l_count' WHERE id = '$ids[$j]'";
          mysqli_query($conn, $sql_update_order_list);
          $sql_update_fill_list = "INSERT INTO fill_list (id1, id2, price, volume)VALUES ('$id', '$ids[$j]', '$bid_prices[$i]', '$count')";
          mysqli_query($conn, $sql_update_fill_list);
          $total_volume+=$count;
          //成交改bid的量
          echo " ";
          echo "volume change2: ";
          echo -$count;
          echo "<br>";
          Edit_Bid_Volume($bid_prices[$i], -$count,$conn);
          $count = 0;
        }
        if ($count==0){
          break;
        }
      }
      if ($count==0){
        $sql_update_order_list = "UPDATE order_list SET filled = '$volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
        mysqli_query($conn, $sql_update_order_list);
        break;
      }
    }
    if($count != 0){
      $sql_update_order_list = "UPDATE order_list SET filled = '$total_volume', unfilled = 0, all_filled = 1 WHERE id = '$id'";
      mysqli_query($conn, $sql_update_order_list);
    }
    echo "end";
  }
  else{
    echo "nothing in bid or ask";
    $sql_update_order_list = "DELETE FROM order_list WHERE id = '$id'";
    mysqli_query($conn, $sql_update_order_list);
  }
}
function Get_Bid_Data($conn){
  $sql = "SELECT * FROM bid ORDER BY price DESC ";
  $result = mysqli_query($conn, $sql);
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $bid_volumes[] = $row['volume'];
            $bid_prices[] = $row['price'];
        }
        $flag = 1;
    }
    else {
      $flag = 0;
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  return array($bid_prices, $bid_volumes, $flag);
}
function Get_Ask_Data($conn){
  $sql = "SELECT * FROM ask ORDER BY price ASC";
  $result = mysqli_query($conn, $sql);
  // 如果有資料
  if ($result) {
    // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
    if (mysqli_num_rows($result)>0) {
        // 取得大於0代表有資料
        // while迴圈會根據資料數量，決定跑的次數
        // mysqli_fetch_assoc方法可取得一筆值
        while ($row = mysqli_fetch_assoc($result)) {
            // 每跑一次迴圈就抓一筆值，最後放進data陣列中
            $ask_volumes[] = $row['volume'];
            $ask_prices[] = $row['price'];
        }
        $flag = 1;
    }
    else {
      $flag = 0;
    }
    // 釋放資料庫查到的記憶體
    mysqli_free_result($result);
  }
  return array($ask_prices, $ask_volumes, $flag);
}
function Edit_Ask_Volume($price, $volume_change, $conn){
  $array_ask = Get_Ask_Data($conn);
  $ask_prices = $array_ask[0];
  $ask_volumes = $array_ask[1];
  echo "ask volume: ";
  echo $ask_volumes[array_search($price, $ask_prices)];
  $volume = $ask_volumes[array_search($price, $ask_prices)]+$volume_change;
  if ($volume == 0){
    $sql = "DELETE FROM ask WHERE price = '$price'";
    mysqli_query($conn, $sql);
  }
  else {
    $sql = "UPDATE ask SET volume = '$volume' WHERE price = '$price'";
    mysqli_query($conn, $sql);
  }
}
function Edit_Bid_Volume($price, $volume_change, $conn){
  $array_bid = Get_Bid_Data($conn);
  $bid_prices = $array_bid[0];
  $bid_volumes = $array_bid[1];
  $volume = $bid_volumes[array_search($price, $bid_prices)]+$volume_change;
  if ($volume == 0){
    $sql = "DELETE FROM bid WHERE price = '$price'";
    mysqli_query($conn, $sql);
  }
  else {
    $sql = "UPDATE bid SET volume = '$volume' WHERE price = '$price'";
    mysqli_query($conn, $sql);
  }
}
$conn->close();


/*if($volume < $ask_volumes[array_search($price, $ask_prices)]){
      $r_volume = $ask_volumes[array_search($price, $ask_prices)]-$volume;
      $fill = $volume;
      $sql = "SELECT id, unfilled, volume FROM order_list WHERE price = '{$price}' and all_filled = 0 and request = 'sell'";
      $result = mysqli_query($conn, $sql);
      if ($result) {
        // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
        if (mysqli_num_rows($result)>0) {
            // 取得大於0代表有資料
            // while迴圈會根據資料數量，決定跑的次數
            // mysqli_fetch_assoc方法可取得一筆值
            while ($row = mysqli_fetch_assoc($result)) {
                // 每跑一次迴圈就抓一筆值，最後放進data陣列中
                $ids[] = $row['id'];
                $unfilled[] = $row['unfilled'];
                $volumes[] = $row['volume'];
            }
        }
        // 釋放資料庫查到的記憶體
        mysqli_free_result($result);
      }
      $count = $volume;
      for ($i=0; $i<count($ids); $i++){
        echo $count;
        echo $unfilled[$i];
        if($count >= $unfilled[$i]){
          $count = $count-$unfilled[$i];
          $sql = "UPDATE order_list SET filled = '{$volumes[$i]}', unfilled = 0, all_filled = 1 WHERE id = '{$ids[$i]}'";
          //成交
          mysqli_query($conn, $sql);
          echo $count;
        } 
        else if ($count < $unfilled[$i]){
          $l_count = $unfilled[$i]-$count;
          $filled = $volumes[$i]-$l_count;
          $sql = "UPDATE order_list SET filled = '{$filled}', unfilled = '{$l_count}' WHERE id = '{$ids[$i]}'";
          //成交
          mysqli_query($conn, $sql);
          $count = 0;
        }
        if ($count==0){
          break;
        }
      }
      $sql = "UPDATE ask SET volume = '{$r_volume}' WHERE price = '{$price}'";
      mysqli_query($conn, $sql);
      $sql = "UPDATE order_list SET filled = '{$fill}', unfilled = 0 WHERE id = '{$id}'";
      mysqli_query($conn, $sql);
      $sql = "UPDATE order_list SET all_filled = 1 WHERE id = '{$id}'";
      mysqli_query($conn, $sql);
    }
    else if($volume >= $ask_volumes[array_search($price, $ask_prices)]){
      $volume = $volume-$ask_volumes[array_search($price, $ask_prices)];
      $fill = $ask_volumes[array_search($price, $ask_prices)];
      $sql = "SELECT id, unfilled, volume FROM order_list WHERE price = '{$price}' and all_filled = 0 and request = 'sell'";
      $result = mysqli_query($conn, $sql);
      if ($result) {
        // mysqli_num_rows方法可以回傳我們結果總共有幾筆資料
        if (mysqli_num_rows($result)>0) {
            // 取得大於0代表有資料
            // while迴圈會根據資料數量，決定跑的次數
            // mysqli_fetch_assoc方法可取得一筆值
            while ($row = mysqli_fetch_assoc($result)) {
                // 每跑一次迴圈就抓一筆值，最後放進data陣列中
                $ids[] = $row['id'];
                $unfilled[] = $row['unfilled'];
                $volumes[] = $row['volume'];
            }
        }
        // 釋放資料庫查到的記憶體
        mysqli_free_result($result);
      }
      $count = $volume+$ask_volumes[array_search($price, $ask_prices)];
      for ($i=0; $i<count($ids); $i++){
        echo $count;
        echo $unfilled[$i];
        if($count >= $unfilled[$i]){
          echo "!";
          $count = $count-$unfilled[$i];
          $sql = "UPDATE order_list SET filled = '{$volumes[$i]}', unfilled = 0, all_filled = 1 WHERE id = '{$ids[$i]}'";
          //成交
          mysqli_query($conn, $sql);
          echo $count;
        } 
        else if ($count < $unfilled[$i]){
          echo "?";
          $l_count = $unfilled[$i]-$count;
          echo $l_count;
          echo " ";
          echo $volumes[$i];
          $filled = $volumes[$i]-$l_count;
          echo $filled;
          $sql = "UPDATE order_list SET filled = '{$filled}', unfilled = '{$l_count}' WHERE id = '{$ids[$i]}'";
          //成交
          mysqli_query($conn, $sql);
          $count = 0;
        }
        if ($count==0){
          break;
        }
      }
      $sql = "DELETE FROM ask WHERE price = '{$price}'";
      mysqli_query($conn, $sql);
      if($volume>0){
        $sql = "INSERT INTO bid VALUES ('{$price}', '{$volume}')";
        mysqli_query($conn, $sql);
      }
      else {
        $sql = "UPDATE order_list SET all_filled = 1 WHERE id = '{$id}'";
        mysqli_query($conn, $sql);
      }
      $sql = "UPDATE order_list SET filled = '{$fill}', unfilled = '{$volume}' WHERE id = '{$id}'";
      mysqli_query($conn, $sql);
      
    }
    echo "have same price in ask";*/
header("Location: trade.php");


