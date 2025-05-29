<?php
require_once "baza.php";

$q = $_GET['q'] ?? '';

if (strlen($q) >= 2) {
    $stmt = $conn->prepare("
        SELECT o.opakowanie_id, CONCAT(p.nazwa_handlowa, ' (', o.opis, ')') AS nazwa
        FROM produkty p
        JOIN opakowania o ON p.medicine_id = o.medicine_id
        WHERE p.nazwa_handlowa LIKE CONCAT('%', ?, '%')
        ORDER BY p.nazwa_handlowa
        LIMIT 15
    ");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $result = $stmt->get_result();
    $wyniki = [];
    while ($row = $result->fetch_assoc()) {
        $wyniki[] = $row;
    }
    echo json_encode($wyniki);
}
