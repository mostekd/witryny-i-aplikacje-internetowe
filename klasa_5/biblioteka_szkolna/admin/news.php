<?php
session_start();

echo '<link rel="stylesheet" href="../css/styl_admin.css">';
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

// Obsługa dodawania/edycji newsa
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $tytul = $conn->real_escape_string($_POST['tytul']);
    $wstep = $conn->real_escape_string($_POST['wstep']);
    $tresc = $conn->real_escape_string($_POST['tresc']);
    $autor = $conn->real_escape_string($_POST['autor']);

    // Obsługa przesłanego zdjęcia
    $zdjecie_id = null;
    if (isset($_FILES['zdjecie']) && $_FILES['zdjecie']['error'] === 0) {
        $file = $_FILES['zdjecie'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $filepath = '../images/news/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $sql = "insert into zdjecia (sciezka, opis) values ('$filepath', '$tytul')";
            $conn->query($sql);
            $zdjecie_id = $conn->insert_id;
        }
    }

    if ($id) {
        // Aktualizacja istniejącego newsa
        $sql = "update news set 
                tytul='$tytul', 
                wstep='$wstep', 
                tresc='$tresc', 
                autor='$autor'";
        if ($zdjecie_id) {
            $sql .= ", zdjecie_id=$zdjecie_id";
        }
        $sql .= " where id=$id";
        
        if ($conn->query($sql)) {
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Zaktualizowano news #$id')");
        }
    } else {
        // Dodanie nowego newsa
        $sql = "insert into news (tytul, wstep, tresc, autor, zdjecie_id) 
                values ('$tytul', '$wstep', '$tresc', '$autor', " . 
                ($zdjecie_id ? $zdjecie_id : 'null') . ")";
        
        if ($conn->query($sql)) {
            $news_id = $conn->insert_id;
            $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Dodano nowy news #$news_id')");
        }
    }
}

// Usuwanie newsa
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn->query("delete from news where id=$id")) {
        $conn->query("insert into logi_admin (admin_id, akcja) values ($admin_id, 'Usunięto news #$id')");
    }
    header('Location: news.php');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Zarządzanie newsami</h1>
            <button onclick="showNewsForm()" class="btn-primary">Dodaj nowy news</button>
        </div>

        <!-- Formularz dodawania/edycji newsa -->
        <div id="newsForm" class="admin-form" style="display: none;">
            <h2>Dodaj/Edytuj news</h2>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" id="newsId">
                <div class="form-group">
                    <label>Tytuł</label>
                    <input type="text" name="tytul" required>
                </div>
                <div class="form-group">
                    <label>Wstęp</label>
                    <textarea name="wstep" required></textarea>
                </div>
                <div class="form-group">
                    <label>Treść</label>
                    <textarea name="tresc" required class="content-editor"></textarea>
                </div>
                <div class="form-group">
                    <label>Autor</label>
                    <input type="text" name="autor" required>
                </div>
                <div class="form-group">
                    <label>Zdjęcie</label>
                    <input type="file" name="zdjecie" accept="image/*">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn-submit">Zapisz</button>
                    <button type="button" onclick="hideNewsForm()" class="btn-cancel">Anuluj</button>
                </div>
            </form>
        </div>

        <!-- Lista newsów -->
        <div class="admin-table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tytuł</th>
                        <th>Data publikacji</th>
                        <th>Autor</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "select id, tytul, data_publikacji, autor from news order by data_publikacji desc";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><?= $row['data_publikacji'] ?></td>
                        <td><?= htmlspecialchars($row['autor']) ?></td>
                        <td class="actions">
                            <button onclick="editNews(<?= $row['id'] ?>)" class="btn-edit">Edytuj</button>
                            <button onclick="deleteNews(<?= $row['id'] ?>)" class="btn-delete">Usuń</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function showNewsForm() {
    document.getElementById('newsForm').style.display = 'block';
    document.getElementById('newsId').value = '';
}

function hideNewsForm() {
    document.getElementById('newsForm').style.display = 'none';
}

function editNews(id) {
    fetch(`api/get_news.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('newsId').value = data.id;
            document.querySelector('input[name="tytul"]').value = data.tytul;
            document.querySelector('textarea[name="wstep"]').value = data.wstep;
            document.querySelector('textarea[name="tresc"]').value = data.tresc;
            document.querySelector('input[name="autor"]').value = data.autor;
            showNewsForm();
        });
}

function deleteNews(id) {
    if (confirm('Czy na pewno chcesz usunąć ten news?')) {
        window.location.href = `news.php?delete=${id}`;
    }
}
</script>

<?php include 'layout/footer.php'; ?>