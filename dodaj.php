<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Odbierz dane z formularza
    $nazwa = $_POST['nazwa'];
    $ilosc = $_POST['ilosc'];

    // Zapisz dane do pliku
    $plik = fopen("apteczka.txt", "a");
    fwrite($plik, "Lek: " . $nazwa . " | Ilość: " . $ilosc . " szt.\n");
    fclose($plik);

    // Przekierowanie po zapisaniu
    echo "<h2>Dodano lek do apteczki!</h2>";
    echo "<a href='index.php'>Wróć do formularza</a>";
}
?>
