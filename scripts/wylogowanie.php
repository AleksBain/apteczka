<?php
session_start();
session_unset(); // opcjonalne: usuwa wszystkie zmienne sesji
session_destroy();
header("Location: /apteczka/index.php"); // użyj ścieżki bezwzględnej
exit;
