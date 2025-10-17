<?php
session_start();

echo '<link rel="stylesheet" href="../css/styl_admin.css">';
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Obsługa formularza książki
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $tytul = $conn->real_escape_string($_POST['tytul']);
    $autor = $conn->real_escape_string($_POST['autor']);
    $wydawnictwo = $conn->real_escape_string($_POST['wydawnictwo']);
    $rok_wydania = (int)$_POST['rok_wydania'];
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $kategoria_id = (int)$_POST['kategoria_id'];
    $aktywna = isset($_POST['aktywna']) ? 1 : 0;
    $uwagi = $conn->real_escape_string($_POST['uwagi']);

    // Obsługa zdjęcia okładki
    $zdjecie_id = null;
    if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] === 0) {
        $file = $_FILES['zdjecie'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $filepath = '../images/books/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $sql = "insert into zdjecia (sciezka, opis) values ('$filepath', '$tytul')";
            $conn->query($sql);
            $zdjecie_id = $conn->insert_id;
        }
    }

    if ($id) {
        // Aktualizacja książki
        $sql = "update ksiazki set 
                tytul='$tytul', 
                autor='$autor', 
                wydawnictwo='$wydawnictwo', 
                rok_wydania=$rok_wydania,
                isbn='$isbn',
                kategoria_id=$kategoria_id,
                aktywna=$aktywna,
                uwagi='$uwagi'";
        if ($zdjecie_id) {
            $sql .= ", zdjecie_id=$zdjecie_id";
        }
        $sql .= " where id=$id";
        
        if ($conn->query($sql)) {
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Zaktualizowano książkę #$id')");
        }
    } else {
        // Dodanie nowej książki
        $sql = "insert into ksiazki (tytul, autor, wydawnictwo, rok_wydania, isbn, kategoria_id, aktywna, uwagi, zdjecie_id) 
                values ('$tytul', '$autor', '$wydawnictwo', $rok_wydania, '$isbn', $kategoria_id, $aktywna, '$uwagi', " . 
                ($zdjecie_id ? $zdjecie_id : 'null') . ")";
        
        if ($conn->query($sql)) {
            $book_id = $conn->insert_id;
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Dodano nową książkę #$book_id')");
        }
    }
}

// Obsługa formularza kategorii
if (isset($_POST['add_category'])) {
    $nazwa = $conn->real_escape_string($_POST['nazwa']);
    if ($conn->query("insert into kategorie (nazwa) values ('$nazwa')")) {
        $cat_id = $conn->insert_id;
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Dodano nową kategorię #$cat_id')");
    }
}

// Usuwanie książki
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("delete from ksiazki where id=$id")) {
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Usunięto książkę #$id')");
    }
    header('Location: books.php');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Zarządzanie książkami</h1>
            <div class="header-buttons">
                <button onclick="showCategoryForm()" class="btn-secondary">Dodaj kategorię</button>
                <button onclick="showBookForm()" class="btn-primary">Dodaj książkę</button>
            </div>
        </div>

        <!-- Formularz kategorii -->
        <div id="categoryForm" class="admin-form" style="display: none;">
            <h2>Dodaj kategorię</h2>
            <form method="post">
                <div class="form-group">
                    <label>Nazwa kategorii</label>
                    <input type="text" name="nazwa" required>
                </div>
                <div class="form-buttons">
                    <button type="submit" name="add_category" class="btn-submit">Dodaj</button>
                    <button type="button" onclick="hideCategoryForm()" class="btn-cancel">Anuluj</button>
                </div>
            </form>
        </div>

        <!-- Formularz książki -->
        <div id="bookForm" class="admin-form" style="display: none;">
            <h2>Dodaj/Edytuj książkę</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="bookId">
                <div class="form-group">
                    <label>Tytuł</label>
                    <input type="text" name="tytul" required>
                </div>
                <div class="form-group">
                    <label>Autor</label>
                    <input type="text" name="autor" required>
                </div>
                <div class="form-group">
                    <label>Wydawnictwo</label>
                    <input type="text" name="wydawnictwo" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Rok wydania</label>
                        <input type="number" name="rok_wydania" required min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="form-group">
                        <label>ISBN</label>
                        <input type="text" name="isbn" required pattern="\d{10}|\d{13}">
                    </div>
                </div>
                <div class="form-group">
                    <label>Kategoria</label>
                    <select name="kategoria_id" required>
                        <option value="">Wybierz kategorię</option>
                        <?php
                        $cats = $conn->query("select * from kategorie order by nazwa");
                        while ($cat = $cats->fetch_assoc()) {
                            echo "<option value='{$cat['id']}'>{$cat['nazwa']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="aktywna" checked>
                        Książka aktywna (dostępna do wypożyczenia)
                    </label>
                </div>
                <div class="form-group">
                    <label>Uwagi</label>
                    <textarea name="uwagi"></textarea>
                </div>
                <div class="form-group">
                    <label>Zdjęcie okładki</label>
                    <input type="file" name="zdjecie" accept="image/*">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Zapisz</button>
                    <button type="button" onclick="hideBookForm()" class="btn-cancel">Anuluj</button>
                </div>
            </form>
        </div>

        <!-- Lista książek -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Kategoria</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select k.*, kat.nazwa as kategoria 
                            from ksiazki k 
                            left join kategorie kat on k.kategoria_id = kat.id 
                            order by k.tytul";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><?= htmlspecialchars($row['autor']) ?></td>
                        <td><?= htmlspecialchars($row['kategoria']) ?></td>
                        <td>
                            <span class="status-<?= $row['aktywna'] ? 'active' : 'inactive' ?>">
                                <?= $row['aktywna'] ? 'Aktywna' : 'Nieaktywna' ?>
                            </span>
                        </td>
                        <td class="actions">
                            <button onclick="editBook(<?= $row['id'] ?>)" class="btn-edit">Edytuj</button>
                            <button onclick="deleteBook(<?= $row['id'] ?>)" class="btn-delete">Usuń</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function showCategoryForm() {
    document.getElementById('categoryForm').style.display = 'block';
    document.getElementById('bookForm').style.display = 'none';
}

function hideCategoryForm() {
    document.getElementById('categoryForm').style.display = 'none';
}

function showBookForm() {
    document.getElementById('bookForm').style.display = 'block';
    document.getElementById('categoryForm').style.display = 'none';
    document.getElementById('bookId').value = '';
}

function hideBookForm() {
    document.getElementById('bookForm').style.display = 'none';
}

function editBook(id) {
    fetch(`api/get_book.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('bookId').value = data.id;
            document.querySelector('input[name="tytul"]').value = data.tytul;
            document.querySelector('input[name="autor"]').value = data.autor;
            document.querySelector('input[name="wydawnictwo"]').value = data.wydawnictwo;
            document.querySelector('input[name="rok_wydania"]').value = data.rok_wydania;
            document.querySelector('input[name="isbn"]').value = data.isbn;
            document.querySelector('select[name="kategoria_id"]').value = data.kategoria_id;
            document.querySelector('input[name="aktywna"]').checked = data.aktywna == 1;
            document.querySelector('textarea[name="uwagi"]').value = data.uwagi;
            showBookForm();
        });
}

function deleteBook(id) {
    if (confirm('Czy na pewno chcesz usunąć tę książkę?')) {
        window.location.href = `books.php?delete=${id}`;
    }
}
</script>

<?php include 'layout/footer.php'; ?>