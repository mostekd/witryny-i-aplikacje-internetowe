<?php
/**
 * Zarządzanie książkami
 */

$message = '';
$messageType = '';
$subaction = isset($_GET['subaction']) ? sanitize($_GET['subaction']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obsługa dodawania/edycji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'add' || $post_action === 'edit') {
        $data = [
            'tytul' => sanitize($_POST['tytul'] ?? ''),
            'autor' => sanitize($_POST['autor'] ?? ''),
            'wydawnictwo' => sanitize($_POST['wydawnictwo'] ?? ''),
            'rok_wydania' => !empty($_POST['rok_wydania']) ? intval($_POST['rok_wydania']) : null,
            'isbn' => sanitize($_POST['isbn'] ?? ''),
            'aktywna' => isset($_POST['aktywna']) ? 1 : 0,
            'uwagi' => sanitize($_POST['uwagi'] ?? ''),
            'ilosc_kopii' => !empty($_POST['ilosc_kopii']) ? intval($_POST['ilosc_kopii']) : 1
        ];

        if (empty($data['tytul']) || empty($data['autor'])) {
            $message = 'Tytuł i autor są wymagane.';
            $messageType = 'danger';
        } else {
            if ($post_action === 'add') {
                if ($ksiazkaObj->add($data)) {
                    $message = 'Książka dodana pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy dodawaniu książki.';
                    $messageType = 'danger';
                }
            } else {
                $book_id = intval($_POST['book_id']);
                if ($ksiazkaObj->update($book_id, $data)) {
                    $message = 'Książka zaktualizowana pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy aktualizacji książki.';
                    $messageType = 'danger';
                }
            }
        }
    } elseif ($post_action === 'delete' && isset($_POST['book_id'])) {
        if ($ksiazkaObj->delete(intval($_POST['book_id']))) {
            $message = 'Książka usunięta pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu książki.';
            $messageType = 'danger';
        }
    }
}

// Pobieranie książki do edycji
$bookToEdit = null;
if ($subaction === 'edit' && $id > 0) {
    $bookToEdit = $ksiazkaObj->getById($id);
}
?>

<h2><i class="fas fa-book"></i> Zarządzanie książkami</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($subaction === 'add' || $subaction === 'edit'): ?>
    <!-- FORMULARZ DODAWANIA/EDYCJI -->
    <div class="form-section">
        <h3><?php echo $subaction === 'add' ? 'Dodaj nową książkę' : 'Edytuj książkę'; ?></h3>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="<?php echo $subaction; ?>">
            <?php if ($bookToEdit): ?>
                <input type="hidden" name="book_id" value="<?php echo $bookToEdit['id']; ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="tytul">Tytuł *</label>
                    <input type="text" id="tytul" name="tytul" 
                           value="<?php echo ($bookToEdit['tytul'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="autor">Autor *</label>
                    <input type="text" id="autor" name="autor" 
                           value="<?php echo ($bookToEdit['autor'] ?? ''); ?>"
                           required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="wydawnictwo">Wydawnictwo</label>
                    <input type="text" id="wydawnictwo" name="wydawnictwo" 
                           value="<?php echo ($bookToEdit['wydawnictwo'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="rok_wydania">Rok wydania</label>
                    <input type="number" id="rok_wydania" name="rok_wydania" 
                           min="1900" max="2100"
                           value="<?php echo ($bookToEdit['rok_wydania'] ?? ''); ?>">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" 
                           value="<?php echo ($bookToEdit['isbn'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="ilosc_kopii">Liczba kopii</label>
                    <input type="number" id="ilosc_kopii" name="ilosc_kopii" 
                           min="1" value="<?php echo ($bookToEdit['ilosc_kopii'] ?? 1); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="uwagi">Uwagi</label>
                <textarea id="uwagi" name="uwagi"><?php echo ($bookToEdit['uwagi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="aktywna" value="1" 
                           <?php echo (isset($bookToEdit['aktywna']) && $bookToEdit['aktywna']) ? 'checked' : ''; ?>>
                    Książka aktywna (dostępna do wypożyczenia)
                </label>
            </div>

            <div class="form-group btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz
                </button>
                <a href="?action=ksiazki" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- LISTA KSIĄŻEK -->
    <div style="margin-bottom: 1rem;">
        <a href="?action=ksiazki&subaction=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj nową książkę
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Wydawnictwo</th>
                    <th>Rok</th>
                    <th>ISBN</th>
                    <th>Kopie</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $booksResult = $ksiazkaObj->getAll(false);
                    if ($booksResult && $booksResult->num_rows > 0):
                        while ($book = $booksResult->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo ($book['tytul']); ?></td>
                        <td><?php echo ($book['autor']); ?></td>
                        <td><?php echo ($book['wydawnictwo'] ?? '-'); ?></td>
                        <td><?php echo $book['rok_wydania'] ?? '-'; ?></td>
                        <td><?php echo ($book['isbn'] ?? '-'); ?></td>
                        <td><?php echo $book['ilosc_kopii']; ?></td>
                        <td>
                            <?php echo $book['aktywna'] ? '<span style="color: green;"><i class="fas fa-check"></i> Aktywna</span>' : '<span style="color: red;"><i class="fas fa-ban"></i> Nieaktywna</span>'; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=ksiazki&subaction=edit&id=<?php echo $book['id']; ?>" class="btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edytuj
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tę książkę?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                    <button type="submit" class="btn-sm btn-delete" style="border: none; padding: 0.5rem 1rem; cursor: pointer;">
                                        <i class="fas fa-trash"></i> Usuń
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem;">Brak książek w bazie</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
