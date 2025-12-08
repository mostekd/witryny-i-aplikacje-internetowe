<?php
/**
 * Strona wyszukiwania książek + lista dostępnych książek
 */

require_once __DIR__ . '/../database/Ksiazka.php';

$ksiazkaObj = new Ksiazka();
$results = null;
$searched = false;

$search_tytul = '';
$search_autor = '';
$search_rok = '';

// DOMYŚLNIE – pobierz wszystkie AKTYWNE książki
$allBooks = $ksiazkaObj->getAll(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_tytul = isset($_POST['tytul']) ? sanitize($_POST['tytul']) : '';
    $search_autor = isset($_POST['autor']) ? sanitize($_POST['autor']) : '';
    $search_rok = isset($_POST['rok_wydania']) ? sanitize($_POST['rok_wydania']) : '';
    
    $results = $ksiazkaObj->search($search_tytul, $search_autor, $search_rok, true);
    $searched = true;
}
?>

<h1><i class="fas fa-book"></i> Katalog książek</h1>
<p>Wyszukaj interesujące Cię książki w naszym katalogu. Wyświetlane są tylko książki dostępne.</p>

<!-- FORMULARZ WYSZUKIWANIA -->
<div class="form-section">
    <h3>Wyszukiwanie</h3>
    <form method="POST" class="form">
        <div class="form-group">
            <label for="tytul">Tytuł:</label>
            <input type="text" id="tytul" name="tytul" 
                   placeholder="Wpisz fragment tytułu" 
                   value="<?php echo htmlspecialchars($search_tytul, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <label for="autor">Autor:</label>
            <input type="text" id="autor" name="autor" 
                   placeholder="Wpisz imię lub nazwisko autora" 
                   value="<?php echo htmlspecialchars($search_autor, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <label for="rok_wydania">Rok wydania:</label>
            <input type="number" id="rok_wydania" name="rok_wydania" 
                   placeholder="Np. 2020"
                   value="<?php echo htmlspecialchars($search_rok, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Szukaj
            </button>
            <button type="reset" class="btn btn-secondary" style="margin-left: 1rem;">
                <i class="fas fa-redo"></i> Wyczyść
            </button>
        </div>
    </form>
</div>

<hr style="margin: 2rem 0;">

<?php if ($searched): ?>

    <!-- WYNIKI WYSZUKIWANIA -->
    <?php if ($results && $results->num_rows > 0): ?>
        <h2>Rezultaty wyszukiwania (<?php echo $results->num_rows; ?> książek)</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Wydawnictwo</th>
                        <th>Rok</th>
                        <th>ISBN</th>
                        <th>Dostępność</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $results->fetch_assoc()): ?>
                        <?php $dostepne = $ksiazkaObj->getAvailableCopies($book['id']); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['tytul'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($book['autor'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($book['wydawnictwo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $book['rok_wydania'] ?? '-'; ?></td>
                            <td><?php echo htmlspecialchars($book['isbn'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php if ($dostepne > 0): ?>
                                    <span style="color: green;"><i class="fas fa-check"></i> <?php echo $dostepne; ?></span>
                                <?php else: ?>
                                    <span style="color: red;"><i class="fas fa-times"></i> Wypożyczona</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Nie znaleziono książek spełniających Twoje kryteria.
        </div>
    <?php endif; ?>

<?php else: ?>

    <!-- DOMYŚLNA LISTA WSZYSTKICH DOSTĘPNYCH KSIĄŻEK -->
    <h2><i class="fas fa-list"></i> Dostępne książki</h2>

    <?php if ($allBooks && $allBooks->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Wydawnictwo</th>
                        <th>Rok</th>
                        <th>ISBN</th>
                        <th>Dostępność</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $allBooks->fetch_assoc()): ?>
                        <?php $dostepne = $ksiazkaObj->getAvailableCopies($book['id']); ?>
                        <?php if ($dostepne > 0): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['tytul'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($book['autor'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($book['wydawnictwo'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo $book['rok_wydania'] ?? '-'; ?></td>
                            <td><?php echo htmlspecialchars($book['isbn'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <span style="color: green;"><i class="fas fa-check"></i> <?php echo $dostepne; ?></span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Brak dostępnych książek w katalogu.
        </div>
    <?php endif; ?>

<?php endif; ?>
