<?php
$host = "localhost";  
$username = "root";         
$password = "root";         
$database = "quiz_db";      
$port = 3309;

$conn = new mysqli($host, $username, $password, $database,$port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>