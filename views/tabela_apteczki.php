<h2></h2>
<table>
    <thead>
        <tr>
            <th>Lek</th>
            <th>Opis opakowania</th>
            <th>Ilość</th>
            <th>Cena</th>
            <th>Termin ważności</th>
            <th>Rozchód</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($lek = $apteczka->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($lek['nazwa_handlowa']) ?></td>
                <td><?= htmlspecialchars($lek['opis']) ?></td>
                <td><?= $lek['ilosc'] ?></td>
                <td><?= $lek['cena'] ?> zł</td>
                <td><?= $lek['termin_waznosci'] ?></td>
                <td>
                    <form method="post">
                        <input type="hidden" name="rozchod_id" value="<?= $lek['inwentarz_id'] ?>">
                        <input type="number" name="rozchod_ilosc" min="1" max="<?= $lek['ilosc'] ?>" required>
                        <select name="typ" required>
                            <option value="użycie">użycie</option>
                            <option value="utylizacja">utylizacja</option>
                        </select>
                        <button type="submit" name="rozchod">➖ Wydaj</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
