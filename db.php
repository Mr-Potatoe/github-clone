<?php
$host = 'localhost';
$db = 'gihub_db';
$user = 'root';
$pass = '';
$port = '3307';

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
