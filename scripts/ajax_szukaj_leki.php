<?php
require_once __DIR__ . '/../baza.php';

$q = $_GET['q'] ?? '';

if (strlen($q) >= 2) {
    $stmt = $conn->prepare("
        SELECT o.opakowanie_id, p.nazwa_handlowa, p.substancja_czynna, p.moc, p.postac_farmaceutyczna, o.opis
        FROM produkty p
        JOIN opakowania o ON p.medicine_id = o.medicine_id
        WHERE p.nazwa_handlowa LIKE CONCAT('%', ?, '%')
        ORDER BY p.nazwa_handlowa
        LIMIT 10
    ");
    $stmt->bind_param("s", $q);
    $stmt->execute();
    $result = $stmt->get_result();
    $output = [];

    while ($row = $result->fetch_assoc()) {
        $output[] = $row;
    }

    echo json_encode($output);
}
