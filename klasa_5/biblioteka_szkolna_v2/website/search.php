<?php
require_once __DIR__ . '/../database/Ksiazka.php';
require_once __DIR__ . '/../database/config.php';

$ksiazkaObj = new Ksiazka();
$db = Database::getInstance()->getConnection();

/* ===============================
   USTAWIENIA PAGINACJI I SORTOWANIA
================================= */
$limit = 10;
$page = isset($_GET['p']) ? max(1, intval($_GET['p'])) : 1;
$offset = ($page - 1) * $limit;

$sort = $_GET['sort'] ?? 'tytul';
$order = $_GET['order'] ?? 'ASC';

$allowedSort = ['tytul', 'autor', 'rok_wydania'];
$allowedOrder = ['ASC', 'DESC'];

if (!in_array($sort, $allowedSort)) $sort = 'tytul';
if (!in_array($order, $allowedOrder)) $order = 'ASC';

/* ===============================
   WYSZUKIWANIE
================================= */
$search_tytul = $_POST['tytul'] ?? '';
$search_autor = $_POST['autor'] ?? '';
$search_rok = $_POST['rok_wydania'] ?? '';
$searched = $_SERVER['REQUEST_METHOD'] === 'POST';

/* ===============================
   SQL – TYLKO DOSTĘPNE KSIĄŻKI
================================= */
$sqlBase = "
FROM ksiazki k
WHERE k.aktywna = TRUE
AND (k.ilosc_kopii >
    (SELECT COUNT(*) 
     FROM wypozyczenia w 
     WHERE w.id_ksiazki = k.id 
     AND w.data_zwrotu IS NULL)
)";

if ($searched) {
    if ($search_tytul)
        $sqlBase .= " AND k.tytul LIKE '%" . sanitize($search_tytul) . "%'";
    if ($search_autor)
        $sqlBase .= " AND k.autor LIKE '%" . sanitize($search_autor) . "%'";
    if ($search_rok)
        $sqlBase .= " AND k.rok_wydania = " . intval($search_rok);
}

/* ===============================
   ILOŚĆ REKORDÓW
================================= */
$countQuery = $db->query("SELECT COUNT(*) as cnt $sqlBase");
$total = $countQuery->fetch_assoc()['cnt'];
$totalPages = max(1, ceil($total / $limit));

/* ===============================
   POBIERANIE DANYCH
================================= */
$sql = "
SELECT k.* 
$sqlBase
ORDER BY $sort $order
LIMIT $limit OFFSET $offset";

$results = $db->query($sql);
?>

<h1><i class="fas fa-book"></i> Katalog książek</h1>
<p>Wyświetlane są wyłącznie książki aktualnie dostępne do wypożyczenia.</p>

<!-- ===========================
     WYSZUKIWANIE
=========================== -->
<div class="form-section">
    <h3>Wyszukiwanie</h3>
    <form method="POST" class="form">

        <div class="form-group">
            <label>Tytuł:</label>
            <input type="text" name="tytul" value="<?= ($search_tytul) ?>">
        </div>

        <div class="form-group">
            <label>Autor:</label>
            <input type="text" name="autor" value="<?= ($search_autor) ?>">
        </div>

        <div class="form-group">
            <label>Rok wydania:</label>
            <input type="number" name="rok_wydania" value="<?= ($search_rok) ?>">
        </div>

        <button class="btn btn-primary">
            <i class="fas fa-search"></i> Szukaj
        </button>
    </form>
</div>

<hr>

<!-- ===========================
     SORTOWANIE
=========================== -->
<form method="GET" style="display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap;">
    <select name="sort">
        <option value="tytul" <?= $sort === 'tytul' ? 'selected' : '' ?>>Tytuł</option>
        <option value="autor" <?= $sort === 'autor' ? 'selected' : '' ?>>Autor</option>
        <option value="rok_wydania" <?= $sort === 'rok_wydania' ? 'selected' : '' ?>>Rok</option>
    </select>

    <select name="order">
        <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>Rosnąco</option>
        <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>Malejąco</option>
    </select>

    <button class="btn btn-primary">
        <i class="fas fa-sort"></i> Sortuj
    </button>
</form>

<!-- ===========================
     WYNIKI
=========================== -->
<h2>Dostępne książki (<?= $total ?>)</h2>

<?php if ($results && $results->num_rows > 0): ?>
<div class="table-container">
<table>
    <thead>
        <tr>
            <th>Tytuł</th>
            <th>Autor</th>
            <th>Rok</th>
            <th>ISBN</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($book = $results->fetch_assoc()): ?>
        <tr>
            <td><?= ($book['tytul']) ?></td>
            <td><?= ($book['autor']) ?></td>
            <td><?= ($book['rok_wydania']) ?></td>
            <td><?= ($book['isbn']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>
<?php else: ?>
<div class="alert alert-info">
    Brak dostępnych książek.
</div>
<?php endif; ?>

<!-- ===========================
     PAGINACJA
=========================== -->
<div style="margin-top:2rem; text-align:center;">
<?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?p=<?= $i ?>&sort=<?= $sort ?>&order=<?= $order ?>"
       class="btn <?= $i == $page ? 'btn-success' : 'btn-secondary' ?>"
       style="margin:3px;">
       <?= $i ?>
    </a>
<?php endfor; ?>
</div>
