<?php
$host = "mysql.agh.edu.pl";
$user = "bainczyk";
$password = "nytt_losenord";
$dbname = "bainczyk";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
