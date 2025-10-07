<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/header.php';

// fetch banner images (active)
$stmt = $pdo->query("SELECT * FROM banner_images WHERE active=1 ORDER BY ordering LIMIT 3");
$banners = $stmt->fetchAll();

// fetch latest news
$newsStmt = $pdo->query("SELECT id,title,excerpt,published_at,image FROM news ORDER BY published_at DESC LIMIT 10");
$news = $newsStmt->fetchAll();
?>

<section class="banner">
  <div class="carousel">
    <?php if ($banners): ?>
      <?php foreach ($banners as $b): ?>
        <div class="slide"><img src="/klasa_5/biblioteka_szkolna/uploads/<?php echo h($b['filename']); ?>" alt="<?php echo h($b['alt']); ?>"></div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="slide"><img src="/klasa_5/biblioteka_szkolna/default-banner1.jpg" alt="banner"></div>
    <?php endif; ?>
  </div>
</section>

<section class="panels">
  <div class="panel widgets">
    <h3>Widgety</h3>
    <div id="calendar">[kalendarz]</div>
    <div id="weather">[pogoda - tu można podpiąć skrypt JS]</div>
    <div id="random-photo">
      <?php
      // random image from news images
      $imgStmt = $pdo->query("SELECT image FROM news WHERE image IS NOT NULL AND image <> ''");
      $imgs = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
      if (!empty($imgs)) {
        $pick = $imgs[array_rand($imgs)];
        echo '<img src="/klasa_5/biblioteka_szkolna/uploads/' . h($pick) . '" alt="losowe">';
      } else {
        echo '<img src="/klasa_5/biblioteka_szkolna/default-photo.jpg" alt="photo">';
      }
      ?>
    </div>
  </div>

  <div class="panel menu-panel">
    <h3>Menu animowane</h3>
    <ul class="animated-menu">
      <li><a href="/klasa_5/biblioteka_szkolna/index.php">Aktualności</a></li>
      <li><a href="/klasa_5/biblioteka_szkolna/books.php">Księgozbiór</a></li>
      <li><a href="/klasa_5/biblioteka_szkolna/guestbook.php">Księga gości</a></li>
      <li><a href="/klasa_5/biblioteka_szkolna/contact.php">Kontakt</a></li>
    </ul>
  </div>

  <div class="panel content-panel">
    <h2>Aktualności</h2>
    <?php foreach ($news as $n): ?>
      <article class="news-item">
        <h3><?php echo h($n['title']); ?></h3>
        <time><?php echo h($n['published_at']); ?></time>
        <p><?php echo h($n['excerpt']); ?></p>
        <p><a class="btn" href="/klasa_5/biblioteka_szkolna/article.php?id=<?php echo (int)$n['id']; ?>">więcej</a></p>
      </article>
    <?php endforeach; ?>

    <p><a class="btn" href="books.php">Pokaż księgozbiór</a></p>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
