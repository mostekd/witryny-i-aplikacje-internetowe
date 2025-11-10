<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }

$db = new Database();

// delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $db->prepare("delete from students where id = ?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
    header("Location: students.php");
    exit;
}

// filters
$q = $_GET['q'] ?? '';
$class = $_GET['class'] ?? '';
$status = $_GET['status'] ?? ''; // not used now, placeholder

$where = ["1=1"]; $params=[]; $types='';
if ($q!=='') { $where[] = "(first_name like ? or last_name like ? or pesel like ?)"; $like="%$q%"; $params[]=$like;$params[]=$like;$params[]=$like;$types.='sss'; }
if ($class!=='') { $where[] = "notes like ?"; $params[]="%class:$class%"; $types.='s'; } // sample
$sql = "select * from students where ".implode(' and ', $where)." order by last_name asc";
$stmt = $db->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>uczestnicy / uczniowie</title>
<link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>👨‍🎓 uczniowie</h2>
  <nav>
    <a href="dashboard.php">panel</a>
    <a href="logout.php">wyloguj</a>
  </nav>
</header>
<main>
  <div class="card">
    <form method="get" style="display:flex;gap:10px;flex-wrap:wrap;">
      <input type="text" name="q" placeholder="imię, nazwisko, pesel" value="<?= htmlspecialchars($q) ?>">
      <input type="text" name="class" placeholder="klasa (filtr)" value="<?= htmlspecialchars($class) ?>">
      <button type="submit">filtruj</button>
      <a href="students.php"><button type="button">wyczyść</button></a>
      <a href="students.php?action=add"><button type="button">➕ dodaj</button></a>
    </form>

    <?php
    if (isset($_GET['action']) && $_GET['action']==='add' && $_SERVER['REQUEST_METHOD']==='POST') {
        $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $db->prepare("insert into students (first_name,last_name,login,password_hash,email,notes,created_at) values (?,?,?,?,?,? , now())");
        $stmt->bind_param("ssssss", $_POST['first_name'], $_POST['last_name'], $_POST['login'], $hash, $_POST['email'], $_POST['notes']);
        $stmt->execute();
        header("Location: students.php");
        exit;
    }
    if (isset($_GET['action']) && $_GET['action']==='add'): ?>
      <form method="post" style="margin-top:12px;">
        <label>imię</label><input name="first_name" required>
        <label>nazwisko</label><input name="last_name" required>
        <label>login (do logowania)</label><input name="login" required>
        <label>hasło</label><input name="password" required>
        <label>email</label><input name="email">
        <label>notatki</label><input name="notes" placeholder="np. class:A">
        <input type="submit" value="dodaj ucznia">
      </form>
    <?php endif; ?>

    <table>
      <tr><th>id</th><th>imię</th><th>nazwisko</th><th>login</th><th>email</th><th>akcje</th></tr>
      <?php while ($r = $res->fetch_assoc()): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= htmlspecialchars($r['first_name']) ?></td>
        <td><?= htmlspecialchars($r['last_name']) ?></td>
        <td><?= htmlspecialchars($r['login'] ?? '-') ?></td>
        <td><?= htmlspecialchars($r['email']) ?></td>
        <td>
          <a href="students.php?edit=<?= $r['id'] ?>">edytuj</a> |
          <a href="students.php?delete=<?= $r['id'] ?>" onclick="return confirm('usunąć?')">usuń</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </table>

    <?php
    // edit
    if (isset($_GET['edit'])) {
        $id = (int)$_GET['edit'];
        if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['do_edit'])) {
            $stmt = $db->prepare("update students set first_name=?, last_name=?, email=?, notes=? where id=?");
            $stmt->bind_param("ssssi", $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['notes'], $id);
            $stmt->execute();
            header("Location: students.php");
            exit;
        }
        $row = $db->query("select * from students where id = $id")->fetch_assoc();
        if ($row):
    ?>
      <form method="post" style="margin-top:12px;">
        <label>imię</label><input name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>">
        <label>nazwisko</label><input name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>">
        <label>email</label><input name="email" value="<?= htmlspecialchars($row['email']) ?>">
        <label>notatki</label><input name="notes" value="<?= htmlspecialchars($row['notes']) ?>">
        <input type="submit" name="do_edit" value="zapisz">
      </form>
    <?php endif; } ?>

  </div>
</main>
</body>
</html>
