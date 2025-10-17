<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Obsługa formularza ucznia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $imie = $conn->real_escape_string($_POST['imie']);
    $nazwisko = $conn->real_escape_string($_POST['nazwisko']);
    $pesel = $conn->real_escape_string($_POST['pesel']);
    $email = $conn->real_escape_string($_POST['email']);
    $klasa = $conn->real_escape_string($_POST['klasa']);
    $uwagi = $conn->real_escape_string($_POST['uwagi']);

    if ($id) {
        // Aktualizacja ucznia
        $sql = "update uczniowie set 
                imie='$imie',
                nazwisko='$nazwisko',
                pesel='$pesel',
                email='$email',
                klasa='$klasa',
                uwagi='$uwagi'
                where id=$id";
        
        if ($conn->query($sql)) {
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Zaktualizowano dane ucznia #$id')");
        }
    } else {
        // Dodanie nowego ucznia
        $sql = "insert into uczniowie (imie, nazwisko, pesel, email, klasa, uwagi) 
                values ('$imie', '$nazwisko', '$pesel', '$email', '$klasa', '$uwagi')";
        
        if ($conn->query($sql)) {
            $student_id = $conn->insert_id;
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Dodano nowego ucznia #$student_id')");
            
            // Utworzenie preferencji użytkownika
            $conn->query("insert into preferencje_uzytkownika (uzytkownik_id) values ($student_id)");
        }
    }
}

// Usuwanie ucznia
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("delete from uczniowie where id=$id")) {
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Usunięto ucznia #$id')");
    }
    header('Location: students.php');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Zarządzanie uczniami</h1>
            <button onclick="showStudentForm()" class="btn-primary">Dodaj ucznia</button>
        </div>

        <!-- Formularz ucznia -->
        <div id="studentForm" class="admin-form" style="display: none;">
            <h2>Dodaj/Edytuj ucznia</h2>
            <form method="post">
                <input type="hidden" name="id" id="studentId">
                <div class="form-row">
                    <div class="form-group">
                        <label>Imię</label>
                        <input type="text" name="imie" required>
                    </div>
                    <div class="form-group">
                        <label>Nazwisko</label>
                        <input type="text" name="nazwisko" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>PESEL</label>
                        <input type="text" name="pesel" required pattern="[0-9]{11}" maxlength="11">
                    </div>
                    <div class="form-group">
                        <label>Klasa</label>
                        <input type="text" name="klasa">
                    </div>
                </div>
                <div class="form-group">
                    <label>E-mail</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Uwagi</label>
                    <textarea name="uwagi"></textarea>
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Zapisz</button>
                    <button type="button" onclick="hideStudentForm()" class="btn-cancel">Anuluj</button>
                </div>
            </form>
        </div>

        <!-- Lista uczniów -->
        <div class="admin-table-container">
            <div class="table-filters">
                <input type="text" id="searchStudent" placeholder="Szukaj ucznia..." oninput="filterStudents(this.value)">
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imię i nazwisko</th>
                        <th>Klasa</th>
                        <th>PESEL</th>
                        <th>E-mail</th>
                        <th>Wypożyczenia</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody id="studentsTableBody">
                    <?php
                    $sql = "select u.*, 
                           (select count(*) from wypozyczenia where uczen_id=u.id and status='wypozyczona') as active_loans
                           from uczniowie u 
                           order by u.nazwisko, u.imie";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nazwisko'] . ' ' . $row['imie']) ?></td>
                        <td><?= htmlspecialchars($row['klasa']) ?></td>
                        <td><?= htmlspecialchars($row['pesel']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <span class="badge <?= $row['active_loans'] > 0 ? 'badge-warning' : 'badge-success' ?>">
                                <?= $row['active_loans'] ?> książek
                            </span>
                        </td>
                        <td class="actions">
                            <button onclick="editStudent(<?= $row['id'] ?>)" class="btn-edit">Edytuj</button>
                            <button onclick="viewLoans(<?= $row['id'] ?>)" class="btn-view">Wypożyczenia</button>
                            <button onclick="deleteStudent(<?= $row['id'] ?>)" class="btn-delete">Usuń</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Modal z historią wypożyczeń -->
        <div id="loansModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Historia wypożyczeń</h2>
                <div id="loansHistory"></div>
            </div>
        </div>
    </main>
</div>

<script>
function showStudentForm() {
    document.getElementById('studentForm').style.display = 'block';
    document.getElementById('studentId').value = '';
}

function hideStudentForm() {
    document.getElementById('studentForm').style.display = 'none';
}

function editStudent(id) {
    fetch(`api/get_student.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('studentId').value = data.id;
            document.querySelector('input[name="imie"]').value = data.imie;
            document.querySelector('input[name="nazwisko"]').value = data.nazwisko;
            document.querySelector('input[name="pesel"]').value = data.pesel;
            document.querySelector('input[name="email"]').value = data.email;
            document.querySelector('input[name="klasa"]').value = data.klasa;
            document.querySelector('textarea[name="uwagi"]').value = data.uwagi;
            showStudentForm();
        });
}

function deleteStudent(id) {
    if (confirm('Czy na pewno chcesz usunąć tego ucznia?')) {
        window.location.href = `students.php?delete=${id}`;
    }
}

function viewLoans(id) {
    fetch(`api/get_student_loans.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('loansModal');
            const history = document.getElementById('loansHistory');
            let html = '<table class="modal-table">';
            html += '<tr><th>Książka</th><th>Data wypożyczenia</th><th>Data zwrotu</th><th>Status</th></tr>';
            
            data.forEach(loan => {
                html += `<tr>
                    <td>${loan.tytul}</td>
                    <td>${loan.data_wypozyczenia}</td>
                    <td>${loan.data_zwrotu || '-'}</td>
                    <td><span class="status-${loan.status}">${loan.status}</span></td>
                </tr>`;
            });
            
            html += '</table>';
            history.innerHTML = html;
            modal.style.display = 'block';
        });
}

// Zamykanie modalu
document.querySelector('.close').onclick = function() {
    document.getElementById('loansModal').style.display = 'none';
}

// Filtrowanie uczniów
function filterStudents(query) {
    query = query.toLowerCase();
    const rows = document.getElementById('studentsTableBody').getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(query) ? '' : 'none';
    }
}
</script>

<?php include 'layout/footer.php'; ?>