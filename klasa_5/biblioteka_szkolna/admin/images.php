<?php
session_start();
require_once '../database/db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: index.php"); exit; }
$db = new Database();

// upload
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['image'])) {
    $u = $_FILES['image'];
    if ($u['error']===0 && $u['size']>0) {
        $allowed = ['image/jpeg','image/png','image/gif'];
        if (in_array($u['type'],$allowed)) {
            $dstDir = __DIR__ . '/../images';
            if (!is_dir($dstDir)) mkdir($dstDir,0755,true);
            $name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $u['name']);
            $dst = $dstDir . '/' . $name;
            if (move_uploaded_file($u['tmp_name'],$dst)) {
                $stmt = $db->prepare("insert into images (file_name, alt_text, title, uploaded_by) values (?,?,?,?)");
                $alt = $_POST['alt'] ?? '';
                $title = $_POST['title'] ?? '';
                $uid = $_SESSION['admin_id'];
                $stmt->bind_param('sssi',$name,$alt,$title,$uid);
                $stmt->execute();
                header('Location: images.php'); exit;
            }
        }
    }
}

// delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $row = $db->query("select * from images where id = $id")->fetch_assoc();
    if ($row) {
        $file = __DIR__ . '/../images/' . $row['file_name'];
        if (file_exists($file)) @unlink($file);
        $stmt = $db->prepare("delete from images where id = ?");
        $stmt->bind_param('i',$id);
        $stmt->execute();
    }
    header('Location: images.php'); exit;
}

$images = $db->query("select * from images order by uploaded_at desc")->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>zarządzanie obrazami</title>
  <link rel="stylesheet" href="../static/css/admin.css">
</head>
<body>
<header>
  <h2>🖼️ obrazy</h2>
  <nav><a href="dashboard.php">panel</a><a href="logout.php">wyloguj</a></nav>
</header>
<main>
  <div class="card">
    <form method="post" enctype="multipart/form-data">
      <label>Plik (jpg/png/gif)</label>
      <input type="file" name="image" required>
      <label>Alt</label>
      <input name="alt">
      <label>Tytuł (opcjonalny)</label>
      <input name="title">
      <input type="submit" value="Prześlij">
    </form>

    <h3>Lista obrazów</h3>
    <table>
      <tr><th>mini</th><th>plik</th><th>tytuł</th><th>data</th><th>akcje</th></tr>
      <?php foreach ($images as $img): ?>
        <tr>
          <td><img src="../images/<?= htmlspecialchars($img['file_name']) ?>" style="height:40px;object-fit:cover;border-radius:4px;"></td>
          <td><?= htmlspecialchars($img['file_name']) ?></td>
          <td><?= htmlspecialchars($img['title']) ?></td>
          <td><?= htmlspecialchars($img['uploaded_at']) ?></td>
          <td><a href="images.php?delete=<?= $img['id'] ?>" onclick="return confirm('usunąć?')">usuń</a></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</main>
</body>
</html>
