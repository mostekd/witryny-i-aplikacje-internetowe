<?php
require_once '../database/models/news.php';
$newsModel = new News();
$articles = $newsModel->get_published_news();
require_once '../database/models/images.php';
$imgModel = new Images();
$banner_images = $imgModel->get_random_images(3);
$random_image = $imgModel->get_random_image();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
<script src="../static/js/main.js" defer></script>
</head>
<body>
<?php include 'includes/menu.php'; ?>

<!-- banner -->
<div class="banner" id="site-banner">
  <?php if (!empty($banner_images)): ?>
    <?php foreach ($banner_images as $idx => $img): ?>
      <?php $path = '../images/' . $img['file_name']; $has = file_exists(__DIR__ . '/../images/' . $img['file_name']); ?>
      <div class="slide<?= $idx===0 ? ' active' : '' ?>" style="background-image: <?= $has ? "url('{$path}')" : "linear-gradient(135deg,#8ec5ff,#e0c3fc)" ?>;">
        <div class="slide-caption"><?= htmlspecialchars($img['title'] ?? '') ?></div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="slide active" style="background:linear-gradient(135deg,#8ec5ff,#e0c3fc);">
      <div class="slide-caption">Biblioteka Szkolna</div>
    </div>
  <?php endif; ?>
</div>

<!-- trzy panele: lewy-widget, animowane menu, zawartość -->
<div class="homepage-grid">
  <aside class="panel left">
    <h3>Widgety</h3>
    <div id="calendar"></div>
    <h4>Losowe zdjęcie</h4>
    <?php if ($random_image && file_exists(__DIR__ . '/../images/' . $random_image['file_name'])): ?>
      <img src="../images/<?= htmlspecialchars($random_image['file_name']) ?>" alt="<?= htmlspecialchars($random_image['alt_text'] ?? '') ?>" class="random-img">
    <?php else: ?>
      <div class="random-img placeholder">Brak obrazów</div>
    <?php endif; ?>
  </aside>

  <aside class="panel center">
    <h3>Menu</h3>
    <nav class="animated-menu">
      <a href="index.php">Strona główna</a>
      <a href="books.php">Szukaj książek</a>
      <a href="guestbook.php">Księga gości</a>
      <a href="contact.php">Kontakt</a>
    </nav>
  </aside>

  <section class="panel right">
    <h2>Aktualności</h2>
    <?php if (empty($articles)): ?>
      <p>Brak aktualności do wyświetlenia.</p>
    <?php else: ?>
      <?php foreach ($articles as $item): ?>
        <article>
          <h3><?= htmlspecialchars($item['title']) ?></h3>
          <small>Opublikowano: <?= htmlspecialchars($item['published_at']) ?></small>
          <p><?= nl2br(htmlspecialchars($item['excerpt'])) ?></p>
          <p><a href="news.php?slug=<?= urlencode($item['slug']) ?>">więcej »</a></p>
        </article>
        <hr>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
  </div>
</body>
</html>
