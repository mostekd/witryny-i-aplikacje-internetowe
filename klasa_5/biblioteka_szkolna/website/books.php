<?php
require_once '../database/models/book.php';
$bookModel = new Book();

$search = $_GET['q'] ?? '';
$books = $bookModel->get_all_books();

if ($search) {
    $search = strtolower($search);
    $books = array_filter($books, function($b) use ($search) {
        return str_contains(strtolower($b['title']), $search)
            || str_contains(strtolower($b['author']), $search);
    });
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Książki | Biblioteka Szkolna</title>
<link rel="stylesheet" href="../static/css/user.css">
</head>
<body>
<?php include 'includes/menu.php'; ?>
<main>
  <h2>Dostępne książki</h2>

  <form method="get" class="search-form">
    <input type="text" name="q" placeholder="Szukaj tytułu lub autora..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Szukaj</button>
  </form>

  <?php if (empty($books)): ?>
    <p>Brak wyników.</p>
  <?php else: ?>
    <table class="book-table">
      <tr><th>Tytuł</th><th>Autor</th><th>Rok</th><th>Wydawca</th></tr>
      <?php foreach ($books as $b): ?>
      <tr>
        <td><?= htmlspecialchars($b['title']) ?></td>
        <td><?= htmlspecialchars($b['author']) ?></td>
        <td><?= htmlspecialchars($b['year']) ?></td>
        <td><?= htmlspecialchars($b['publisher']) ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  <?php endif; ?>
</main>
</body>
</html>
