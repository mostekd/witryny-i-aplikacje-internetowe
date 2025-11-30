<?php
/**
 * Zarządzanie uczniami
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
            'imie' => sanitize($_POST['imie'] ?? ''),
            'nazwisko' => sanitize($_POST['nazwisko'] ?? ''),
            'pesel' => sanitize($_POST['pesel'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'klasa' => sanitize($_POST['klasa'] ?? ''),
            'uwagi' => sanitize($_POST['uwagi'] ?? ''),
            'aktywny' => isset($_POST['aktywny']) ? 1 : 0
        ];

        if (empty($data['imie']) || empty($data['nazwisko'])) {
            $message = 'Imię i nazwisko są wymagane.';
            $messageType = 'danger';
        } else {
            if ($post_action === 'add') {
                if ($uczenObj->add($data)) {
                    $message = 'Uczeń dodany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy dodawaniu ucznia.';
                    $messageType = 'danger';
                }
            } else {
                $student_id = intval($_POST['student_id']);
                if ($uczenObj->update($student_id, $data)) {
                    $message = 'Uczeń zaktualizowany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy aktualizacji ucznia.';
                    $messageType = 'danger';
                }
            }
        }
    } elseif ($post_action === 'delete' && isset($_POST['student_id'])) {
        if ($uczenObj->delete(intval($_POST['student_id']))) {
            $message = 'Uczeń usunięty pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu ucznia.';
            $messageType = 'danger';
        }
    }
}

// Pobieranie ucznia do edycji
$studentToEdit = null;
if ($subaction === 'edit' && $id > 0) {
    $studentToEdit = $uczenObj->getById($id);
}
?>

<h2><i class="fas fa-users"></i> Zarządzanie uczniami</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($subaction === 'add' || $subaction === 'edit'): ?>
    <!-- FORMULARZ DODAWANIA/EDYCJI -->
    <div class="form-section">
        <h3><?php echo $subaction === 'add' ? 'Dodaj nowego ucznia' : 'Edytuj ucznia'; ?></h3>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="<?php echo $subaction; ?>">
            <?php if ($studentToEdit): ?>
                <input type="hidden" name="student_id" value="<?php echo $studentToEdit['id']; ?>">
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="imie">Imię *</label>
                    <input type="text" id="imie" name="imie" 
                           value="<?php echo ($studentToEdit['imie'] ?? ''); ?>"
                           required>
                </div>

                <div class="form-group">
                    <label for="nazwisko">Nazwisko *</label>
                    <input type="text" id="nazwisko" name="nazwisko" 
                           value="<?php echo ($studentToEdit['nazwisko'] ?? ''); ?>"
                           required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="pesel">PESEL</label>
                    <input type="text" id="pesel" name="pesel" 
                           pattern="[0-9]{11}"
                           value="<?php echo ($studentToEdit['pesel'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="klasa">Klasa</label>
                    <input type="text" id="klasa" name="klasa" 
                           placeholder="Np. 3a, 4b"
                           value="<?php echo ($studentToEdit['klasa'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo ($studentToEdit['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="uwagi">Uwagi</label>
                <textarea id="uwagi" name="uwagi"><?php echo ($studentToEdit['uwagi'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="aktywny" value="1" 
                           <?php echo (isset($studentToEdit['aktywny']) && $studentToEdit['aktywny']) ? 'checked' : ''; ?>>
                    Uczeń aktywny
                </label>
            </div>

            <div class="form-group btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz
                </button>
                <a href="?action=uczniowie" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- LISTA UCZNIÓW -->
    <div style="margin-bottom: 1rem;">
        <a href="?action=uczniowie&subaction=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj nowego ucznia
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>PESEL</th>
                    <th>Klasa</th>
                    <th>E-mail</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $studentsResult = $uczenObj->getAll(false);
                    if ($studentsResult && $studentsResult->num_rows > 0):
                        while ($student = $studentsResult->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo ($student['imie']); ?></td>
                        <td><?php echo ($student['nazwisko']); ?></td>
                        <td><?php echo ($student['pesel'] ?? '-'); ?></td>
                        <td><?php echo ($student['klasa'] ?? '-'); ?></td>
                        <td><?php echo ($student['email'] ?? '-'); ?></td>
                        <td>
                            <?php echo $student['aktywny'] ? '<span style="color: green;"><i class="fas fa-check"></i> Aktywny</span>' : '<span style="color: red;"><i class="fas fa-ban"></i> Nieaktywny</span>'; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=uczniowie&subaction=edit&id=<?php echo $student['id']; ?>" class="btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edytuj
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć tego ucznia?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
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
                        <td colspan="7" style="text-align: center; padding: 2rem;">Brak uczniów w bazie</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
