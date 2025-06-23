<?php
$servername = "sql308.infinityfree.com";
$username = "if0_39232569";
$password = "ZoRNyn6nlyG"; 
$dbname = "if0_39232569_kish";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>