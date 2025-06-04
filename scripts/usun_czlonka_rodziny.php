<?php
session_start();
require_once __DIR__ . '/../baza.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['czlonek_id'])) {
    header("Location: /apteczka/rodzina.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$czlonek_id = (int) $_POST['czlonek_id'];

// Usuwaj tylko, jeśli należy do zalogowanego użytkownika
$stmt = $conn->prepare("DELETE FROM czlonkowie_rodziny WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $czlonek_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: /apteczka/rodzina.php");
exit();
