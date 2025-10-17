<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = executeQuery('SELECT * FROM news WHERE id = ? LIMIT 1', 'i', [$id]);
$a = fetchOne($stmt);
if (!$a) {
  echo '<p>Artykuł nie znaleziony. <a href="index.php">Powrót</a></p>';
  require_once __DIR__ . '/includes/footer.php';
  exit;
}
?>

<article class="full-article">
  <h2><?php echo h($a['title']); ?></h2>
  <time><?php echo h($a['published_at']); ?></time>
  <div class="content"><?php echo nl2br(h($a['content'])); ?></div>
  <p>Autor: <?php echo h($a['author']); ?></p>
  <p><a href="index.php">Powrót</a></p>
</article>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
