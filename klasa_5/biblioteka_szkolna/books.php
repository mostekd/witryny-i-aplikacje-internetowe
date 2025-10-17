<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

// simple search handling
$q = $_GET['q'] ?? '';
$sql = "SELECT * FROM books WHERE active=1";
$params = [];
if ($q !== '') {
  $sql .= " AND (title LIKE ? OR author LIKE ?)
  ";
  $params[] = "%$q%";
  $params[] = "%$q%";
}
$sql .= " ORDER BY title LIMIT 200";
// Create parameter types string based on number of parameters
$types = str_repeat('s', count($params));
$stmt = executeQuery($sql, $types, $params);
$results = fetchAll($stmt);
?>

<h2>Wyszukiwanie książek</h2>
<form method="get">
  <label>Tytuł/autor: <input name="q" value="<?php echo h($q); ?>"></label>
  <button>szukaj</button>
</form>

<?php if ($results): ?>
  <table class="books-table">
  <tr><th>Tytuł</th><th>Autor</th><th>Rok</th><th>Aktywna</th></tr>
  <?php foreach ($results as $r): ?>
    <tr>
      <td><?php echo h($r['title']); ?></td>
      <td><?php echo h($r['author']); ?></td>
      <td><?php echo h($r['year']); ?></td>
      <td><?php echo $r['active'] ? 'tak':'nie'; ?></td>
    </tr>
  <?php endforeach; ?>
  </table>
<?php else: ?>
  <p>Brak wyników.</p>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
