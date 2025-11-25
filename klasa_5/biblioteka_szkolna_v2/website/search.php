<?php
/**
 * Strona wyszukiwania książek
 */

require_once __DIR__ . '/../database/Ksiazka.php';

$ksiazkaObj = new Ksiazka();
$results = null;
$searched = false;
$search_tytul = '';
$search_autor = '';
$search_rok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_tytul = isset($_POST['tytul']) ? sanitize($_POST['tytul']) : '';
    $search_autor = isset($_POST['autor']) ? sanitize($_POST['autor']) : '';
    $search_rok = isset($_POST['rok_wydania']) ? sanitize($_POST['rok_wydania']) : '';
    
    $results = $ksiazkaObj->search($search_tytul, $search_autor, $search_rok, true);
    $searched = true;
}
?>

<h1><i class="fas fa-book"></i> Katalog książek</h1>
<p>Wyszukaj interesujące Cię książki w naszym katalogu. Dostępne są tylko aktywne pozycje.</p>

<!-- FORMULARZ WYSZUKIWANIA -->
<div class="form-section">
    <h3>Wyszukiwanie</h3>
    <form method="POST" class="form">
        <div class="form-group">
            <label for="tytul">Tytuł:</label>
            <input type="text" id="tytul" name="tytul" 
                   placeholder="Wpisz fragment tytułu" 
                   value="<?php echo htmlspecialchars($search_tytul); ?>">
        </div>

        <div class="form-group">
            <label for="autor">Autor:</label>
            <input type="text" id="autor" name="autor" 
                   placeholder="Wpisz imię lub nazwisko autora" 
                   value="<?php echo htmlspecialchars($search_autor); ?>">
        </div>

        <div class="form-group">
            <label for="rok_wydania">Rok wydania:</label>
            <input type="number" id="rok_wydania" name="rok_wydania" 
                   placeholder="Np. 2020"
                   value="<?php echo htmlspecialchars($search_rok); ?>">
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

<!-- WYNIKI WYSZUKIWANIA -->
<?php if ($searched): ?>
    <hr style="margin: 2rem 0;">
    
    <?php if ($results && $results->num_rows > 0): ?>
        <h2>Rezultaty wyszukiwania (<?php echo $results->num_rows; ?> książek)</h2>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tytuł</th>
                        <th>Autor</th>
                        <th>Wydawnictwo</th>
                        <th>Rok wydania</th>
                        <th>ISBN</th>
                        <th>Dostępne kopie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($book = $results->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['tytul']); ?></td>
                            <td><?php echo htmlspecialchars($book['autor']); ?></td>
                            <td><?php echo htmlspecialchars($book['wydawnictwo'] ?? '-'); ?></td>
                            <td><?php echo $book['rok_wydania'] ?? '-'; ?></td>
                            <td><?php echo htmlspecialchars($book['isbn'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                    $dostepne = $ksiazkaObj->getAvailableCopies($book['id']);
                                    echo $dostepne > 0 ? '<span style="color: green;"><i class="fas fa-check"></i> ' . $dostepne . '</span>' : '<span style="color: red;"><i class="fas fa-times"></i> Niedostępna</span>';
                                ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Nie znaleziono książek spełniających Twoje kryteria wyszukiwania.
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info" style="margin-top: 2rem;">
        <i class="fas fa-info-circle"></i> Uzupełnij formularz i naciśnij przycisk <strong>Szukaj</strong>, aby znaleźć interesujące Cię książki.
    </div>
<?php endif; ?>
