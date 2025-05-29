<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rozchod'])) {
    $id = $_POST['rozchod_id'];
    $rozchod = $_POST['rozchod_ilosc'];
    $typ = $_POST['typ'] ?? 'użycie';

    $stmt = $conn->prepare("UPDATE apteczka SET ilosc = ilosc - ? WHERE id = ? AND ilosc >= ?");
    $stmt->bind_param("iii", $rozchod, $id, $rozchod);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<p style='color:green;'>Rozchód wykonany.</p>";

        $stmt_op = $conn->prepare("SELECT opakowanie_id FROM apteczka WHERE id = ?");
        $stmt_op->bind_param("i", $id);
        $stmt_op->execute();
        $result = $stmt_op->get_result();
        $row = $result->fetch_assoc();
        $opakowanie_id = $row['opakowanie_id'];

        $stmt_log = $conn->prepare("INSERT INTO rozchody (rozchody_apteczki_id, rozchody_opakowanie_id, ilosc, typ) VALUES (?, ?, ?, ?)");
        $stmt_log->bind_param("iiis", $id, $opakowanie_id, $rozchod, $typ);
        $stmt_log->execute();
    } else {
        echo "<p style='color:red;'>Nie można wykonać rozchodu (zbyt mała ilość).</p>";
    }
}
