<?php
session_start();

echo '<link rel="stylesheet" href="../css/styl_admin.css">';
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Pobieranie domyślnego okresu wypożyczenia z ustawień
$result = $conn->query("select wartosc from ustawienia where klucz = 'okres_wypozyczenia'");
$okres_wypozyczenia = $result->fetch_assoc()['wartosc'];

// Obsługa formularza wypożyczenia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ksiazka_id = (int)$_POST['ksiazka_id'];
    $uczen_id = (int)$_POST['uczen_id'];
    $data_wypozyczenia = $conn->real_escape_string($_POST['data_wypozyczenia']);
    
    // Sprawdzenie czy książka nie jest już wypożyczona
    $check = $conn->query("select id from wypozyczenia where ksiazka_id = $ksiazka_id and status = 'wypozyczona'");
    if ($check->num_rows === 0) {
        $sql = "insert into wypozyczenia (ksiazka_id, uczen_id, data_wypozyczenia) 
                values ($ksiazka_id, $uczen_id, '$data_wypozyczenia')";
        
        if ($conn->query($sql)) {
            $wypozyczenie_id = $conn->insert_id;
            
            // Dodanie wydarzenia do kalendarza
            $data_zwrotu = date('Y-m-d', strtotime($data_wypozyczenia . " + $okres_wypozyczenia days"));
            $sql = "insert into kalendarz_wydarzenia (uzytkownik_id, tytul, opis, data_rozpoczecia, data_zakonczenia, typ) 
                   values ($uczen_id, 'Zwrot książki', 'Termin zwrotu wypożyczonej książki', '$data_zwrotu 00:00:00', '$data_zwrotu 23:59:59', 'zwrot')";
            $conn->query($sql);
            
            // Logowanie operacji
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Dodano nowe wypożyczenie #$wypozyczenie_id')");
        }
    }
}

// Obsługa zwrotu książki
if (isset($_GET['return'])) {
    $id = (int)$_GET['return'];
    $sql = "update wypozyczenia set 
            status = 'zwrócona',
            data_zwrotu = current_date()
            where id = $id";
    
    if ($conn->query($sql)) {
        // Usunięcie wydarzenia z kalendarza
        $conn->query("delete from kalendarz_wydarzenia where typ = 'zwrot' and uzytkownik_id = (select uczen_id from wypozyczenia where id = $id)");
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Zarejestrowano zwrot książki #$id')");
    }
    header('Location: loans.php');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Zarządzanie wypożyczeniami</h1>
            <button onclick="showLoanForm()" class="btn-primary">Nowe wypożyczenie</button>
        </div>

        <!-- Formularz wypożyczenia -->
        <div id="loanForm" class="admin-form" style="display: none;">
            <h2>Nowe wypożyczenie</h2>
            <form method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label>Uczeń</label>
                        <select name="uczen_id" required>
                            <option value="">Wybierz ucznia...</option>
                            <?php
                            $result = $conn->query("select id, concat(nazwisko, ' ', imie, ' (', klasa, ')') as nazwa from uczniowie order by nazwisko, imie");
                            while ($row = $result->fetch_assoc()):
                            ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nazwa']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Książka</label>
                        <select name="ksiazka_id" required>
                            <option value="">Wybierz książkę...</option>
                            <?php
                            $sql = "select k.id, concat(k.tytul, ' (', k.autor, ')') as nazwa 
                                   from ksiazki k
                                   where k.aktywna = 1 
                                   and not exists (
                                       select 1 from wypozyczenia w 
                                       where w.ksiazka_id = k.id 
                                       and w.status = 'wypozyczona'
                                   )
                                   order by k.tytul";
                            $result = $conn->query($sql);
                            while ($row = $result->fetch_assoc()):
                            ?>
                            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nazwa']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Data wypożyczenia</label>
                        <input type="date" name="data_wypozyczenia" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Okres wypożyczenia</label>
                        <input type="text" value="<?= $okres_wypozyczenia ?> dni" disabled>
                    </div>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Zapisz</button>
                    <button type="button" onclick="hideLoanForm()" class="btn-cancel">Anuluj</button>
                </div>
            </form>
        </div>

        <!-- Lista wypożyczeń -->
        <div class="admin-table-container">
            <div class="table-filters">
                <select onchange="filterLoans(this.value)">
                    <option value="all">Wszystkie</option>
                    <option value="wypozyczona">Wypożyczone</option>
                    <option value="zwrócona">Zwrócone</option>
                    <option value="przeterminowana">Przeterminowane</option>
                </select>
                <input type="text" id="searchLoan" placeholder="Szukaj..." oninput="searchLoans(this.value)">
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Książka</th>
                        <th>Uczeń</th>
                        <th>Data wypożyczenia</th>
                        <th>Planowany zwrot</th>
                        <th>Data zwrotu</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody id="loansTableBody">
                    <?php
                    $sql = "select w.*, 
                           k.tytul, k.autor,
                           concat(u.nazwisko, ' ', u.imie) as uczen,
                           date_add(w.data_wypozyczenia, interval $okres_wypozyczenia day) as planowany_zwrot
                           from wypozyczenia w
                           join ksiazki k on w.ksiazka_id = k.id
                           join uczniowie u on w.uczen_id = u.id
                           order by w.data_wypozyczenia desc";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr class="status-<?= $row['status'] ?>">
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['tytul'] . ' (' . $row['autor'] . ')') ?></td>
                        <td><?= htmlspecialchars($row['uczen']) ?></td>
                        <td><?= $row['data_wypozyczenia'] ?></td>
                        <td><?= $row['planowany_zwrot'] ?></td>
                        <td><?= $row['data_zwrotu'] ?: '-' ?></td>
                        <td>
                            <span class="badge badge-<?= $row['status'] ?>">
                                <?= $row['status'] ?>
                            </span>
                        </td>
                        <td class="actions">
                            <?php if ($row['status'] === 'wypozyczona'): ?>
                            <button onclick="returnBook(<?= $row['id'] ?>)" class="btn-return">Zwrot</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function showLoanForm() {
    document.getElementById('loanForm').style.display = 'block';
}

function hideLoanForm() {
    document.getElementById('loanForm').style.display = 'none';
}

function returnBook(id) {
    if (confirm('Czy na pewno chcesz zarejestrować zwrot tej książki?')) {
        window.location.href = `loans.php?return=${id}`;
    }
}

function filterLoans(status) {
    const rows = document.getElementById('loansTableBody').getElementsByTagName('tr');
    for (let row of rows) {
        if (status === 'all' || row.classList.contains('status-' + status)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function searchLoans(query) {
    query = query.toLowerCase();
    const rows = document.getElementById('loansTableBody').getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        if (row.style.display !== 'none' && text.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

// Aktualizacja statusów wypożyczeń co minutę
setInterval(() => {
    const rows = document.getElementById('loansTableBody').getElementsByTagName('tr');
    for (let row of rows) {
        if (row.classList.contains('status-wypozyczona')) {
            const planowanyZwrot = new Date(row.cells[4].textContent);
            if (planowanyZwrot < new Date()) {
                row.classList.remove('status-wypozyczona');
                row.classList.add('status-przeterminowana');
                row.cells[6].innerHTML = '<span class="badge badge-przeterminowana">przeterminowana</span>';
            }
        }
    }
}, 60000);
</script>

<?php include 'layout/footer.php'; ?>