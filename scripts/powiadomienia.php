<?php
require_once __DIR__ . '/../baza.php';
$user_id = $_SESSION['user_id']; 

$sql = "
SELECT 
    i.inwentarz_id AS id,
    p.nazwa_handlowa AS lek,
    i.termin_waznosci AS data_waznosci,
    a.nazwa_apteczki AS apteczka
FROM inwentarz i
JOIN apteczka a ON i.apteczka_id = a.id_apteczki
JOIN opakowania o ON i.opakowanie_id = o.opakowanie_id
JOIN produkty p ON o.medicine_id = p.medicine_id
LEFT JOIN czlonkowie_rodziny cz ON a.wlasciciel_id = cz.id
WHERE (
    a.wlasciciel_id = ? 
    OR cz.user_id = ?
)
AND i.termin_waznosci < CURDATE()
ORDER BY i.termin_waznosci ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$przeterminowane = $result->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($przeterminowane && count($przeterminowane) > 0): ?>
    <div class="notifications">
        <h2>🔔 Powiadomienia</h2>
        <ul>
            <?php foreach ($przeterminowane as $p): ?>
                <li>
                    ⚠️ Uwaga! Lek <strong><?= htmlspecialchars($p['lek']) ?></strong>
                    w apteczce <strong><?= htmlspecialchars($p['apteczka']) ?></strong>
                    jest przeterminowany (<?= htmlspecialchars($p['data_waznosci']) ?>).
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
