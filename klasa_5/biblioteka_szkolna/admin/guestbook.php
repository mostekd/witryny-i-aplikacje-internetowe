<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
$db = new Database();

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $stmt = $db->prepare("update guestbook_entries set approved = 1, approved_by = ?, approved_at = now() where id = ?");
    $stmt->bind_param("ii", $_SESSION['admin_id'], $id);
    $stmt->execute();
    header("Location: guestbook.php");
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("delete from guestbook_entries where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: guestbook.php");
    exit;
}

$status = $_GET['status'] ?? ''; // pending/approved
$q = $_GET['q'] ?? '';
$where = ['1=1']; $params=[]; $types='';
if ($status==='pending') { $where[] = "approved = 0"; }
if ($status==='approved') { $where[] = "approved = 1"; }
if ($q!=='') { $where[] = "nickname like ? or message like ?"; $like="%$q%"; $params[]=$like;$params[]=$like;$types.='ss'; }

$sql = "select * from guestbook_entries where ".implode(' and ',$where)." order by created_at desc";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute(); $res = $stmt->get_result();
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>księga gości</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>💬 księga gości</h2>
  <nav><a href="dashboard.php">panel</a><a href="logout.php">wyloguj</a></nav>
</header>
<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;">
      <input name="q" placeholder="pseudonim lub tekst" value="<?= htmlspecialchars($q) ?>">
      <select name="status"><option value="">wszystkie</option><option value="pending" <?= $status==='pending'?'selected':'' ?>>oczekujące</option><option value="approved" <?= $status==='approved'?'selected':'' ?>>zatwierdzone</option></select>
      <button>filtruj</button>
    </form>

    <table>
      <tr><th>nick</th><th>email</th><th>wiadomość</th><th>status</th><th>akcje</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['nickname']) ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td><?= nl2br(htmlspecialchars(substr($r['message'],0,200))) ?></td>
        <td><?= $r['approved'] ? 'zatwierdzony' : 'oczekuje' ?></td>
        <td>
          <?php if (!$r['approved']): ?><a href="guestbook.php?approve=<?= $r['id'] ?>">zatwierdź</a> | <?php endif; ?>
          <a href="guestbook.php?delete=<?= $r['id'] ?>" onclick="return confirm('usunąć?')">usuń</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>
</body>
</html>
