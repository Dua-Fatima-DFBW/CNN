<?php
$host = "localhost";
$username = "rsoa_rsoa311_24";
$password = "123456";
$dbname = "rsoa_rsoa311_24";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("Database connection error: " . mysqli_connect_error());
}
?>
