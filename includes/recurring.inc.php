<?php
ob_start(); // Enable output buffering
header("Location: https://carbon-trading.azurewebsites.net/trade.php");
ob_end_flush();