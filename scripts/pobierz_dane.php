<?php
$produkty = $conn->query("
    SELECT o.opakowanie_id, p.nazwa_handlowa, o.opis 
    FROM produkty p
    JOIN opakowania o ON p.medicine_id = o.medicine_id
    ORDER BY p.nazwa_handlowa
    LIMIT 10
");

$apteczka = $conn->query("
    SELECT i.inwentarz_id, p.nazwa_handlowa, o.opis, i.ilosc, i.cena, i.termin_waznosci
    FROM inwentarz i
    JOIN opakowania o ON i.inwentarz_opakowanie_id = o.opakowanie_id
    JOIN produkty p ON o.medicine_id = p.medicine_id
    LIMIT 10
");
