<?php
// PHP Data Objects(PDO) Sample Code:
try {
    $conn = new PDO("sqlsrv:server = tcp:carbon-trading.database.windows.net,1433; Database = carbon-trading", "IMFteam", "NYCUimf5487");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    print("Error connecting to SQL Server.");
    die(print_r($e));
}

// SQL Server Extension Sample Code:
$connectionInfo = array("UID" => "IMFteam", "pwd" => "NYCUimf5487", "Database" => "carbon-trading", "LoginTimeout" => 30, "Encrypt" => 1, "TrustServerCertificate" => 0);
$serverName = "tcp:carbon-trading.database.windows.net,1433";
$conn = sqlsrv_connect($serverName, $connectionInfo);
?>