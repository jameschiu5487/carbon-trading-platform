<?php
ob_start(); // Enable output buffering
session_start();
session_unset();
session_destroy();

header("Location: https://carbon-trading.azurewebsites.net/trade.php");
ob_end_flush();