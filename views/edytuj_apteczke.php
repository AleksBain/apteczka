<?php
session_start();
require_once __DIR__ . '/../baza.php';

$user_id = $_SESSION['user_id'];
$apteczka_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM apteczka WHERE id_apteczki = ? AND wlasciciel_id = ?");
$stmt->bind_param("ii", $apteczka_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("❌ Brak dostępu. Ta apteczka nie należy do Ciebie.");
}

$apteczka = $result->fetch_assoc();

$stmt = $conn->prepare("SELECT nazwa_apteczki FROM apteczka WHERE id_apteczki = ?");
$stmt->bind_param("i", $apteczka_id);
$stmt->execute();
$stmt->bind_result($nazwa_apteczki);
$stmt->fetch();
$stmt->close();

$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$stmt->close();

$stmt = $conn->prepare("SELECT id, imie, relacja FROM czlonkowie_rodziny WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$family_result = $stmt->get_result();

$family_members = [];
while ($family_row = $family_result->fetch_assoc()) {
    $family_members[] = $family_row;
}
$stmt->close();

$sql = "SELECT 
            i.inwentarz_id,
            i.apteczka_id,
            p.nazwa_handlowa, 
            p.substancja_czynna, 
            p.moc, 
            p.postac_farmaceutyczna, 
            i.ilosc, 
            i.cena, 
            i.termin_waznosci, 
            i.opis
        FROM inwentarz i
        JOIN opakowania o ON i.opakowanie_id = o.opakowanie_id
        JOIN produkty p ON o.medicine_id = p.medicine_id
        WHERE i.apteczka_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $apteczka_id);
$stmt->execute();
$leki_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj apteczkę</title>
    <link rel="stylesheet" href="/apteczka/css/n.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: end;
            justify-content: center;
        }
        
        .quantity-input {
            width: 60px;
            display: flex;
            margin-right: 40px;
        }
        
        .suggestions-list {
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .form-container {
            background-color: #f8f9fa;
            width: 100%;
            padding: 40px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .top-form-container {
            display: flex;
            justify-content: center;
            align-items: left;
            margin-bottom: 20px;
            flex-direction: column;
            margin-top: 20px;
            padding: 20px;
        }
        
        .blocking {
            display: block;
        }
        
        .person-select {
            min-width: 150px;
        }
    </style>
</head>
<body>
    <?php include "../scripts/pasek_nawigacyjny.php"; ?>
    <div class="top-form-container">
        
        <h2>✏️ Edytuj apteczkę: <?= htmlspecialchars($nazwa_apteczki) ?></h2>
        
        <button id="toggleFormBtn" class="btn btn-success mb-3">➕ Dodaj lek</button>

        <div id="dodajLekForm" class="form-container"> 
            <form method="POST" action="/apteczka/scripts/dodaj_lek.php" class="row g-3">
                <input type="hidden" name="id_apteczki" value="<?= $apteczka_id ?>">
                <input type="hidden" name="opakowanie_id" id="opakowanie_id">

                <div class="position-relative">
                    <label for="nazwa_input" class="form-label">Nazwa leku</label>
                    <input type="text" 
                           name="nazwa" 
                           id="nazwa_input" 
                           class="form-control" 
                           placeholder="Wpisz nazwę leku" 
                           autocomplete="off" 
                           required>
                    <ul id="suggestions" class="list-group position-absolute w-100 suggestions-list"></ul>
                </div>
                
                <div>
                    <label for="dawka_input" class="form-label">Dawka</label>
                    <input type="text" 
                           name="dawka" 
                           id="dawka_input" 
                           class="form-control" 
                           placeholder="Dawka" 
                           readonly>
                </div>
                
                <div>
                    <label for="jednostka_input" class="form-label">Postać</label>
                    <input type="text" 
                           name="jednostka" 
                           id="jednostka_input" 
                           class="form-control" 
                           placeholder="Postać" 
                           readonly>
                </div>
                
                <div>
                    <label for="ilosc_input" class="form-label">Ilość</label>
                    <input type="number" 
                           name="ilosc" 
                           id="ilosc_input"
                           class="form-control" 
                           placeholder="Ilość" 
                           required 
                           min="1">
                </div>
                
                <div>
                    <label for="cena_input" class="form-label">Cena (zł)</label>
                    <input type="number" 
                           step="0.01" 
                           name="cena" 
                           id="cena_input"
                           class="form-control" 
                           placeholder="Opcjonalnie">
                </div>
                
                <div>
                    <label for="termin_input" class="form-label">Termin ważności</label>
                    <input type="date" 
                           name="termin" 
                           id="termin_input"
                           class="form-control" 
                           required>
                </div>
                
                <div>
                    <button type="submit" name="dodaj" class="btn btn-primary">💾 Zapisz lek</button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary">❌ Anuluj</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nazwa handlowa</th>
                        <th>Substancja czynna</th>
                        <th>Moc</th>
                        <th>Postać</th>
                        <th>Ilość</th>
                        <th>Cena</th>
                        <th>Termin ważności</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $leki_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['nazwa_handlowa']) ?></td>
                            <td><?= htmlspecialchars($row['substancja_czynna']) ?></td>
                            <td><?= htmlspecialchars($row['moc']) ?></td>
                            <td><?= htmlspecialchars($row['postac_farmaceutyczna']) ?></td>
                            <td><?= htmlspecialchars($row['ilosc']) ?></td>
                            <td><?= htmlspecialchars($row['cena']) ?> zł</td>
                            <td><?= htmlspecialchars($row['termin_waznosci']) ?></td>
                            <td class="blocking">
                                <div class="action-buttons">

                                    <form action="../scripts/usun_lek.php" method="POST">
                                        <input type="hidden" name="id" value="<?= $row['inwentarz_id'] ?>">
                                        <input type="hidden" name="id_apteczki" value="<?= $apteczka_id ?>">
                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                onclick="return confirm('Czy na pewno chcesz usunąć ten lek?')"
                                                title="Usuń lek">
                                            🗑️ Usuń
                                        </button>
                                    </form>
                                    

                                    <form action="../scripts/wydaj_lek.php" method="POST" class="action-form">
                                        <input type="hidden" name="inwentarz_id" value="<?= $row['inwentarz_id'] ?>">
                                        <input type="hidden" name="id_apteczki" value="<?= $row['apteczka_id'] ?>">

                                        <div class="hover-inputs">
                                            <input type="number" 
                                                   name="ilosc" 
                                                   class="form-control quantity-input" 
                                                   min="1" 
                                                   max="<?= $row['ilosc'] ?>" 
                                                   placeholder="Ilość"
                                                   required>


                                            <select name="osoba_id" class="form-select form-select-sm mt-1 person-select" required>
                                                <option value="" disabled selected>Wybierz osobę</option>

                                                <option value="user_<?= $user_id ?>">
                                                    👤 <?= htmlspecialchars($user_data['username'])?> (Ja)
                                                </option>
   
                                                <?php foreach ($family_members as $member): ?>
                                                    <option value="family_<?= $member['id'] ?>">
                                                        👥 <?= htmlspecialchars($member['imie'] ) ?>
                                                        <?php if (!empty($member['relacja'])): ?>
                                                            (<?= htmlspecialchars($member['relacja']) ?>)
                                                        <?php endif; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm" title="Wydaj lek">
                                            📤 Wydaj
                                        </button>
                                    </form>

                              
                                    <form action="../scripts/utylizuj_lek.php" method="POST" class="action-form">
                                        <input type="hidden" name="inwentarz_id" value="<?= $row['inwentarz_id'] ?>">
                                        <input type="hidden" name="id_apteczki" value="<?= $row['apteczka_id'] ?>">

                                        <div class="hover-inputs">
                                            <input type="number" 
                                                   name="ilosc" 
                                                   class="form-control quantity-input" 
                                                   min="1" 
                                                   max="<?= $row['ilosc'] ?>" 
                                                   placeholder="Ilość"
                                                   required>
                                        </div>
                                        <button type="submit" 
                                                class="btn btn-warning btn-sm" 
                                                onclick="return confirm('Czy na pewno chcesz utylizować ten lek?')" 
                                                title="Utylizuj lek">
                                            ♻️ Utylizuj
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>


        <div class="mt-4">
            <a href="/apteczka/apteczka.php" class="btn btn-secondary">← Powrót do apteczek</a>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const input = document.getElementById("nazwa_input");
            const suggestions = document.getElementById("suggestions");
            const toggleBtn = document.getElementById("toggleFormBtn");
            const form = document.getElementById("dodajLekForm");
            const cancelBtn = document.getElementById("cancelBtn");

            function toggleForm() {
                const isVisible = form.style.display !== "none";
                form.style.display = isVisible ? "none" : "block";
                toggleBtn.textContent = isVisible ? "➕ Dodaj lek" : "❌ Ukryj formularz";
            }

            toggleBtn.addEventListener("click", toggleForm);
            cancelBtn.addEventListener("click", function() {
                form.style.display = "none";
                toggleBtn.textContent = "➕ Dodaj lek";
                form.querySelector('form').reset();
                suggestions.innerHTML = "";
            });

            input.addEventListener("input", function () {
                const query = this.value.trim();
                
                if (query.length < 2) {
                    suggestions.innerHTML = "";
                    return;
                }

                fetch("/apteczka/scripts/ajax_szukaj_leki.php?q=" + encodeURIComponent(query))
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = "";
                        
                        data.forEach(item => {
                            const li = document.createElement("li");
                            li.classList.add("list-group-item", "list-group-item-action");
                            li.textContent = `${item.nazwa_handlowa} (${item.opis})`;
                            li.style.cursor = "pointer";
                            
                            li.addEventListener("click", function () {
                                input.value = item.nazwa_handlowa;
                                document.getElementById("dawka_input").value = item.moc;
                                document.getElementById("jednostka_input").value = item.postac_farmaceutyczna;
                                document.getElementById("opakowanie_id").value = item.opakowanie_id;
                                
                                if (item.opis) {
                                    const quantityMatch = item.opis.match(/\d+/);
                                    if (quantityMatch) {
                                        document.getElementById("ilosc_input").value = quantityMatch[0];
                                    }
                                }
                                
                                suggestions.innerHTML = "";
                            });
                            
                            suggestions.appendChild(li);
                        });
                    })
                    .catch(error => {
                        console.error('Błąd podczas pobierania sugestii:', error);
                    });
            });

            document.addEventListener("click", function (event) {
                if (!suggestions.contains(event.target) && event.target !== input) {
                    suggestions.innerHTML = "";
                }
            });
        });
    </script>
</body>
</html>