<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();

// obsługa usuwania
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("delete from books where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: books.php");
    exit;
}

// filtry
$q = $_GET['q'] ?? '';
$status = $_GET['status'] ?? ''; // 'available' or 'all' or 'borrowed'
$author = $_GET['author'] ?? '';

$where = ["1=1"];
$params = []; $types = '';
if ($q !== '') { $where[] = "(title like ? or author like ?)"; $like = "%$q%"; $params[] = $like; $params[] = $like; $types .= 'ss'; }
if ($author !== '') { $where[] = "author = ?"; $params[] = $author; $types .= 's'; }
if ($status === 'available') { $where[] = "active = 1"; }
if ($status === 'borrowed') { $where[] = "active = 0"; } // depending on your usage

$sql = "select * from books where ".implode(' and ', $where)." order by title asc";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>zarządzaj książkami</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>📚 książki</h2>
  <nav>
    <a href="dashboard.php">panel</a>
    <a href="add_admin.php">dodaj admina</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input type="text" name="q" placeholder="tytuł lub autor" value="<?= htmlspecialchars($q) ?>">
      <input type="text" name="author" placeholder="autor exact" value="<?= htmlspecialchars($author) ?>">
      <select name="status">
        <option value="">wszystkie</option>
        <option value="available" <?= $status==='available' ? 'selected' : '' ?>>dostępne</option>
        <option value="borrowed" <?= $status==='borrowed' ? 'selected' : '' ?>>wypożyczone</option>
      </select>
      <button type="submit">filtruj</button>
      <a href="books.php"><button type="button">wyczyść</button></a>
      <a href="books.php?action=add"><button type="button">➕ dodaj</button></a>
    </form>

    <?php
    // formularz dodawania (prosty)
    if (isset($_GET['action']) && $_GET['action']==='add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        // handled below
    }
    if (isset($_GET['action']) && $_GET['action']==='add'): ?>
      <form method="post" style="margin-top:15px;">
        <label>tytuł</label><input name="title" required>
        <label>autor</label><input name="author">
        <label>wydawca</label><input name="publisher">
        <label>rok</label><input name="year" type="number">
        <label>isbn</label><input name="isbn">
        <input type="submit" name="do_add" value="dodaj książkę">
      </form>
    <?php endif; ?>

    <?php
    // obsługa dodawania
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_add'])) {
        $stmt = $db->prepare("insert into books (title,author,publisher,year,isbn,active,created_at) values (?,?,?,?,?,1,now())");
        $y = $_POST['year'] ? (int)$_POST['year'] : null;
        $stmt->bind_param("sssis", $_POST['title'], $_POST['author'], $_POST['publisher'], $y, $_POST['isbn']);
        $stmt->execute();
        header("Location: books.php");
        exit;
    }
    ?>

    <table>
      <tr><th>tytuł</th><th>autor</th><th>rok</th><th>wydawca</th><th>akcje</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['author']) ?></td>
        <td><?= htmlspecialchars($r['year']) ?></td>
        <td><?= htmlspecialchars($r['publisher']) ?></td>
        <td>
          <a href="books.php?edit=<?= $r['id'] ?>">edytuj</a> |
          <a href="books.php?delete=<?= $r['id'] ?>" onclick="return confirm('usunąć?')">usuń</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>

    <?php
    // prosty edytor (GET edit)
    if (isset($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['do_edit'])) {
            $stmt = $db->prepare("update books set title=?,author=?,publisher=?,year=?,isbn=?,updated_at=now() where id=?");
            $y = $_POST['year'] ? (int)$_POST['year'] : null;
            $stmt->bind_param("sssisi", $_POST['title'], $_POST['author'], $_POST['publisher'], $y, $_POST['isbn'], $id);
            $stmt->execute();
            header("Location: books.php");
            exit;
        }
        $row = $db->query("select * from books where id = $id")->fetch_assoc();
        if ($row):
    ?>
      <form method="post" style="margin-top:15px;">
        <label>tytuł</label><input name="title" value="<?= htmlspecialchars($row['title']) ?>" required>
        <label>autor</label><input name="author" value="<?= htmlspecialchars($row['author']) ?>">
        <label>wydawca</label><input name="publisher" value="<?= htmlspecialchars($row['publisher']) ?>">
        <label>rok</label><input name="year" type="number" value="<?= htmlspecialchars($row['year']) ?>">
        <label>isbn</label><input name="isbn" value="<?= htmlspecialchars($row['isbn']) ?>">
        <input type="submit" name="do_edit" value="zapisz zmiany">
      </form>
    <?php endif; } ?>
  </div>
</main>
</body>
</html>
