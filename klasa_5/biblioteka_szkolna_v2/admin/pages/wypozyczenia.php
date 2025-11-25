<?php
/**
 * Zarządzanie wypożyczeniami
 */

$message = '';
$messageType = '';
$subaction = isset($_GET['subaction']) ? sanitize($_GET['subaction']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obsługa akcji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'add') {
        $id_ksiazki = intval($_POST['id_ksiazki'] ?? 0);
        $id_ucznia = intval($_POST['id_ucznia'] ?? 0);
        $dni = !empty($_POST['dni_wypozyczenia']) ? intval($_POST['dni_wypozyczenia']) : DEFAULT_LOAN_PERIOD;

        if ($id_ksiazki > 0 && $id_ucznia > 0) {
            if ($wypozyczeniaObj->add($id_ksiazki, $id_ucznia, $dni)) {
                $message = 'Wypożyczenie dodane pomyślnie.';
                $messageType = 'success';
            } else {
                $message = 'Błąd przy dodawaniu wypożyczenia.';
                $messageType = 'danger';
            }
        } else {
            $message = 'Wybierz książkę i ucznia.';
            $messageType = 'danger';
        }
    } elseif ($post_action === 'return' && isset($_POST['rental_id'])) {
        if ($wypozyczeniaObj->returnBook(intval($_POST['rental_id']))) {
            $message = 'Książka zwrócona pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy zwracaniu książki.';
            $messageType = 'danger';
        }
    } elseif ($post_action === 'delete' && isset($_POST['rental_id'])) {
        if ($wypozyczeniaObj->delete(intval($_POST['rental_id']))) {
            $message = 'Wypożyczenie usunięte pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu wypożyczenia.';
            $messageType = 'danger';
        }
    }
}
?>

<h2><i class="fas fa-exchange-alt"></i> Zarządzanie wypożyczeniami</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($subaction === 'add'): ?>
    <!-- FORMULARZ DODAWANIA WYPOŻYCZENIA -->
    <div class="form-section">
        <h3>Dodaj nowe wypożyczenie</h3>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="add">

            <div class="form-group">
                <label for="id_ksiazki">Książka *</label>
                <select id="id_ksiazki" name="id_ksiazki" required>
                    <option value="">-- Wybierz książkę --</option>
                    <?php
                        $booksResult = $ksiazkaObj->getAll(true);
                        if ($booksResult):
                            while ($book = $booksResult->fetch_assoc()):
                    ?>
                        <option value="<?php echo $book['id']; ?>">
                            <?php echo htmlspecialchars($book['tytul'] . ' - ' . $book['autor']); ?>
                        </option>
                    <?php
                            endwhile;
                        endif;
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_ucznia">Uczeń *</label>
                <select id="id_ucznia" name="id_ucznia" required>
                    <option value="">-- Wybierz ucznia --</option>
                    <?php
                        $studentsResult = $uczenObj->getAll(true);
                        if ($studentsResult):
                            while ($student = $studentsResult->fetch_assoc()):
                    ?>
                        <option value="<?php echo $student['id']; ?>">
                            <?php echo htmlspecialchars($student['imie'] . ' ' . $student['nazwisko'] . ' (' . $student['klasa'] . ')'); ?>
                        </option>
                    <?php
                            endwhile;
                        endif;
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="dni_wypozyczenia">Okres wypożyczenia (dni)</label>
                <input type="number" id="dni_wypozyczenia" name="dni_wypozyczenia" 
                       min="1" value="<?php echo DEFAULT_LOAN_PERIOD; ?>">
            </div>

            <div class="form-group btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Utwórz wypożyczenie
                </button>
                <a href="?action=wypozyczenia" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- LISTA WYPOŻYCZEŃ -->
    <div style="margin-bottom: 1rem;">
        <a href="?action=wypozyczenia&subaction=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj nowe wypożyczenie
        </a>
    </div>

    <h3>Aktywne wypożyczenia</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Książka</th>
                    <th>Uczeń</th>
                    <th>Data wypożyczenia</th>
                    <th>Planowany zwrot</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $rentalsResult = $wypozyczeniaObj->getActive();
                    if ($rentalsResult && $rentalsResult->num_rows > 0):
                        while ($rental = $rentalsResult->fetch_assoc()):
                            $days_lent = ceil((time() - strtotime($rental['data_wypozyczenia'])) / 86400);
                            $is_overdue = $days_lent > DEFAULT_LOAN_PERIOD;
                ?>
                    <tr style="<?php echo $is_overdue ? 'background-color: #ffebee;' : ''; ?>">
                        <td><?php echo htmlspecialchars($rental['tytul']); ?></td>
                        <td><?php echo htmlspecialchars($rental['imie'] . ' ' . $rental['nazwisko']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($rental['data_wypozyczenia'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($rental['data_planowanego_zwrotu'])); ?></td>
                        <td>
                            <?php 
                                if ($is_overdue) {
                                    echo '<span style="color: red;"><i class="fas fa-exclamation-triangle"></i> PRZETRZYMANA</span>';
                                } else {
                                    echo '<span style="color: green;"><i class="fas fa-check"></i> Aktywna</span>';
                                }
                            ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="return">
                                    <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                                    <button type="submit" class="btn-sm btn-approve">
                                        <i class="fas fa-undo"></i> Zwrot
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
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
                        <td colspan="6" style="text-align: center; padding: 2rem;">Brak aktywnych wypożyczeń</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr style="margin: 2rem 0;">

    <h3>Historia wypożyczeń</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Książka</th>
                    <th>Uczeń</th>
                    <th>Wypożyczona</th>
                    <th>Zwrócona</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $historyResult = $wypozyczeniaObj->getAll();
                    if ($historyResult && $historyResult->num_rows > 0):
                        $count = 0;
                        while ($rental = $historyResult->fetch_assoc() && $count < 10):
                            if ($rental['data_zwrotu']):
                                $count++;
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($rental['tytul']); ?></td>
                        <td><?php echo htmlspecialchars($rental['imie'] . ' ' . $rental['nazwisko']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($rental['data_wypozyczenia'])); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($rental['data_zwrotu'])); ?></td>
                    </tr>
                <?php
                            endif;
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 2rem;">Brak historii</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
