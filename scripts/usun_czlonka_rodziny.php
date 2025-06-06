<?php
session_start();
require_once __DIR__ . '/../baza.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['czlonek_id'])) {
    header("Location: /apteczka/rodzina.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$czlonek_id = (int) $_POST['czlonek_id'];

$stmt = $conn->prepare("SELECT rodzina_id FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($user_rodzina_id);
$stmt->fetch();
$stmt->close();

if (!$user_rodzina_id) {
    die("Nie znaleziono rodziny użytkownika.");
}

$stmt = $conn->prepare("DELETE FROM czlonkowie_rodziny WHERE id = ? AND rodzina_id = ?");
$stmt->bind_param("ii", $czlonek_id, $user_rodzina_id);
$stmt->execute();
$stmt->close();

header("Location: /apteczka/rodzina.php");
exit();
