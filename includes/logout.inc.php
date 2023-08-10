<?php
ob_start(); // Enable output buffering
session_start();
session_unset();
session_destroy();

header("location: https://carbon-trading.azurewebsites.net/index.php");
ob_end_flush();