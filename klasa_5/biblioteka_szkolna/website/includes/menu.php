<?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
?>
<nav class="main-nav">
  <div class="nav-container">
    <a href="index.php" class="logo">📚 Biblioteka</a>
    <button id="nav-toggle" aria-label="menu" style="background:transparent;border:none;color:#fff;font-size:20px;display:none;">☰</button>
    <div class="nav-links">
      <a href="index.php">Strona główna</a>
      <a href="books.php">Książki</a>
      <a href="guestbook.php">Księga gości</a>
      <a href="contact.php">Kontakt</a>
      <?php if (isset($_SESSION['student_id'])): ?>
        <a href="profile.php">Profil</a>
        <a href="logout.php">Wyloguj</a>
      <?php else: ?>
        <a href="login.php">Zaloguj się</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
