<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
$db = new Database();

// zmiana okresu wypożyczenia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_period_days'])) {
    $days = (int)$_POST['loan_period_days'];
    if ($days <= 0) $days = 14;
    $stmt = $db->prepare("insert into settings (`key`,`value`) values ('loan_period_days',?) on duplicate key update `value` = values(`value`)");
    $stmt->bind_param('s', $days);
    $stmt->execute();
    header('Location: reports.php'); exit;
}

$row = $db->query("select `value` from settings where `key` = 'loan_period_days'")->fetch_assoc();
$loan_period = (int)($row['value'] ?? 14);

// raport: książki przetrzymywane powyżej okresu
$sql_overdue = "select l.*, b.title, s.first_name, s.last_name, datediff(curdate(), date(l.date_borrowed)) as days_borrowed from loans l join books b on l.book_id=b.id join students s on l.student_id=s.id where l.returned = 0 and l.date_borrowed < date_sub(now(), interval $loan_period day) order by l.date_borrowed asc";
$overdue = $db->query($sql_overdue)->fetch_all(MYSQLI_ASSOC);

// raport: wyszukiwanie uczniów po pierwszej literze
$starts = $_GET['starts'] ?? '';
$students_report = [];
if ($starts !== '') {
    $like = $db->escape($starts) . '%';
    $stmt = $db->prepare("select * from students where last_name like ? order by last_name asc");
    $stmt->bind_param('s', $like);
    $stmt->execute();
    $res = $stmt->get_result();
    $students_report = $res->fetch_all(MYSQLI_ASSOC);
    // for each student, fetch current loans
    foreach ($students_report as &$st) {
        $sid = (int)$st['id'];
        $st['loans'] = $db->query("select l.*, b.title from loans l join books b on l.book_id=b.id where l.student_id = $sid order by l.date_borrowed desc")->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>raporty</title>
  <link rel="stylesheet" href="../static/css/admin.css">
  <style>.small-form{display:flex;gap:8px;align-items:center}</style>
</head>
<body>
<header>
  <h2>📊 raporty</h2>
  <nav><a href="dashboard.php">panel</a><a href="logout.php">wyloguj</a></nav>
</header>
<main>
  <div class="card">
    <h4>okres wypożyczenia (dni)</h4>
    <form method="post" class="small-form">
      <input type="number" name="loan_period_days" value="<?= htmlspecialchars($loan_period) ?>" min="1">
      <input type="submit" value="zapisz">
    </form>
  </div>

  <div class="card">
    <h4>książki przetrzymywane dłużej niż <?= htmlspecialchars($loan_period) ?> dni</h4>
    <?php if (empty($overdue)): ?>
      <p>brak przetrzymanych książek powyżej ustawionego okresu</p>
    <?php else: ?>
      <table>
        <tr><th>książka</th><th>uczeń</th><th>data wyp.</th><th>dni przetrzymania</th><th>akcje</th></tr>
        <?php foreach ($overdue as $o): ?>
          <tr>
            <td><?= htmlspecialchars($o['title']) ?></td>
            <td><?= htmlspecialchars($o['first_name'].' '.$o['last_name']) ?></td>
            <td><?= htmlspecialchars($o['date_borrowed']) ?></td>
            <td><?= htmlspecialchars($o['days_borrowed']) ?></td>
            <td><a href="loans.php?q=<?= urlencode($o['title']) ?>">pokaż</a></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

  <div class="card">
    <h4>wyszukiwanie uczniów (po literze nazwiska)</h4>
    <form method="get" style="display:flex;gap:8px;align-items:center;">
      <input name="starts" placeholder="np. A" value="<?= htmlspecialchars($starts) ?>">
      <button type="submit">szukaj</button>
    </form>

    <?php if ($starts !== ''): ?>
      <?php if (empty($students_report)): ?>
        <p>brak uczniów</p>
      <?php else: ?>
        <?php foreach ($students_report as $st): ?>
          <div style="margin-bottom:10px;">
            <strong><?= htmlspecialchars($st['first_name'].' '.$st['last_name']) ?></strong> (id: <?= $st['id'] ?>)
            <?php if (!empty($st['loans'])): ?>
              <ul>
                <?php foreach ($st['loans'] as $l): ?>
                  <li><?= htmlspecialchars($l['title']) ?> - <?= htmlspecialchars($l['date_borrowed']) ?> - <?= $l['returned'] ? 'zwrócona' : 'wypożyczona' ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div>brak wypożyczeń</div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
