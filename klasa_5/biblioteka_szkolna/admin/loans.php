<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();

// oznacz jako zwrócone
if (isset($_GET['return'])) {
    $id = (int)$_GET['return'];
    $stmt = $db->prepare("update loans set returned = 1, date_returned = now() where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    // dodaj wpis do loan_history
    $loan = $db->query("select * from loans where id = $id")->fetch_assoc();
    $stmt2 = $db->prepare("insert into loan_history (loan_id, book_id, student_id, admin_id, action, action_date) values (?,?,?,?,?,now())");
    $action = 'return';
    $stmt2->bind_param("iiiis", $id, $loan['book_id'], $loan['student_id'], $_SESSION['admin_id'], $action);
    $stmt2->execute();

    header("Location: loans.php");
    exit;
}

// filtry
$q = $_GET['q'] ?? '';
$status = $_GET['status'] ?? ''; // wypożyczone/zwrocone
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

$where = ["1=1"]; $params=[]; $types='';
if ($q!=='') { $where[] = "(b.title like ? or s.last_name like ? or s.first_name like ?)"; $like="%$q%"; $params[]=$like;$params[]=$like;$params[]=$like;$types.='sss'; }
if ($status==='borrowed') { $where[] = "l.returned = 0"; }
if ($status==='returned') { $where[] = "l.returned = 1"; }
if ($date_from) { $where[] = "l.date_borrowed >= ?"; $params[] = $date_from; $types.='s'; }
if ($date_to) { $where[] = "l.date_borrowed <= ?"; $params[] = $date_to; $types.='s'; }

$sql = "select l.*, b.title, s.first_name, s.last_name from loans l join books b on l.book_id=b.id join students s on l.student_id=s.id where ".implode(' and ', $where)." order by l.date_borrowed desc";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>wypożyczenia</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>📖 wypożyczenia</h2>
  <nav>
    <a href="dashboard.php">panel</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input name="q" placeholder="tytuł lub nazwisko ucznia" value="<?= htmlspecialchars($q) ?>">
      <select name="status">
        <option value="">wszystkie</option>
        <option value="borrowed" <?= $status==='borrowed' ? 'selected' : '' ?>>wypożyczone</option>
        <option value="returned" <?= $status==='returned' ? 'selected' : '' ?>>zwrócone</option>
      </select>
      <label>od</label><input type="date" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
      <label>do</label><input type="date" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
      <button type="submit">filtruj</button>
    </form>

    <table>
      <tr><th>tytuł</th><th>uczeń</th><th>data wypoż.</th><th>termin</th><th>status</th><th>akcje</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($r['title']) ?></td>
        <td><?= htmlspecialchars($r['first_name'].' '.$r['last_name']) ?></td>
        <td><?= htmlspecialchars($r['date_borrowed']) ?></td>
        <td><?= htmlspecialchars($r['date_due']) ?></td>
        <td><?= $r['returned'] ? '✅ zwrócona' : '📖 wypożyczona' ?></td>
        <td>
          <?php if (!$r['returned']): ?>
            <a href="loans.php?return=<?= $r['id'] ?>" onclick="return confirm('oznaczyć jako zwrócone?')">oznacz jako zwrócone</a>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>
  </div>
</main>
</body>
</html>
