<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
$db = new Database();

// delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("delete from news where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: news.php");
    exit;
}

// add
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['do_add'])) {
    $stmt = $db->prepare("insert into news (title, slug, excerpt, content, author, is_published, published_at, created_at) values (?,?,?,?,?, ?, now(), now())");
    $is_pub = isset($_POST['is_published']) ? 1 : 0;
    $slug = trim($_POST['slug'])?: strtolower(preg_replace('/[^a-z0-9]+/','-', $_POST['title']));
    $stmt->bind_param("sssssii", $_POST['title'], $slug, $_POST['excerpt'], $_POST['content'], $_POST['author'], $is_pub);
    $stmt->execute();
    header("Location: news.php");
    exit;
}

// filters
$q = $_GET['q'] ?? '';
$status = $_GET['status'] ?? ''; // published/draft

$where = ['1=1']; $params=[]; $types='';
if ($q!=='') { $where[] = "title like ?"; $params[]="%$q%"; $types.='s'; }
if ($status==='published') { $where[] = "is_published = 1"; }
if ($status==='draft') { $where[] = "is_published = 0"; }

$sql = "select * from news where ".implode(' and ', $where)." order by published_at desc";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>aktualności</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>📰 aktualności</h2>
  <nav><a href="dashboard.php">panel</a><a href="logout.php">wyloguj</a></nav>
</header>
<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input name="q" placeholder="szukaj po tytule" value="<?= htmlspecialchars($q) ?>">
      <select name="status"><option value="">wszystkie</option><option value="published" <?= $status==='published'?'selected':'' ?>>opublikowane</option><option value="draft" <?= $status==='draft'?'selected':'' ?>>szkic</option></select>
      <button>filtruj</button>
      <a href="news.php?action=add"><button type="button">➕ dodaj</button></a>
    </form>

    <?php if (isset($_GET['action']) && $_GET['action']==='add'): ?>
      <form method="post" style="margin-top:12px;">
        <label>tytuł</label><input name="title" required>
        <label>slug (opcjonalny)</label><input name="slug">
        <label>krótki opis</label><input name="excerpt">
        <label>autor</label><input name="author">
        <label>treść</label><textarea name="content" rows="6"></textarea>
        <label><input type="checkbox" name="is_published"> opublikuj natychmiast</label>
        <input type="submit" name="do_add" value="dodaj">
      </form>
    <?php endif; ?>

    <table>
      <tr><th>tytuł</th><th>autor</th><th>status</th><th>data</th><th>akcje</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['author']) ?></td>
        <td><?= $r['is_published'] ? 'opublikowane' : 'szkic' ?></td>
        <td><?= htmlspecialchars($r['published_at'] ?? $r['created_at']) ?></td>
        <td>
          <a href="news.php?edit=<?= $r['id'] ?>">edytuj</a> |
          <a href="news.php?delete=<?= $r['id'] ?>" onclick="return confirm('usunąć?')">usuń</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>

    <?php
    // edycja
    if (isset($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['do_edit'])) {
            $is_pub = isset($_POST['is_published'])?1:0;
            $stmt = $db->prepare("update news set title=?, slug=?, excerpt=?, content=?, author=?, is_published=?, updated_at=now() where id=?");
            $slug = trim($_POST['slug'])?: strtolower(preg_replace('/[^a-z0-9]+/','-', $_POST['title']));
            $stmt->bind_param("sssssii", $_POST['title'], $slug, $_POST['excerpt'], $_POST['content'], $_POST['author'], $is_pub, $id);
            $stmt->execute();
            header("Location: news.php");
            exit;
        }
        $row = $db->query("select * from news where id = $id")->fetch_assoc();
        if ($row):
    ?>
      <form method="post" style="margin-top:12px;">
        <label>tytuł</label><input name="title" value="<?= htmlspecialchars($row['title']) ?>">
        <label>slug</label><input name="slug" value="<?= htmlspecialchars($row['slug']) ?>">
        <label>excerpt</label><input name="excerpt" value="<?= htmlspecialchars($row['excerpt']) ?>">
        <label>autor</label><input name="author" value="<?= htmlspecialchars($row['author']) ?>">
        <label>treść</label><textarea name="content" rows="6"><?= htmlspecialchars($row['content']) ?></textarea>
        <label><input type="checkbox" name="is_published" <?= $row['is_published'] ? 'checked' : '' ?>> opublikuj</label>
        <input type="submit" name="do_edit" value="zapisz">
      </form>
    <?php endif; } ?>

  </div>
</main>
</body>
</html>
