<?php
/**
 * Zarządzanie banerami
 */

$message = '';
$messageType = '';
$subaction = isset($_GET['subaction']) ? sanitize($_GET['subaction']) : '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obsługa akcji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'add' || $post_action === 'edit') {
        $data = [
            'sciezka_zdjecia' => sanitize($_POST['sciezka_zdjecia'] ?? ''),
            'tytul' => sanitize($_POST['tytul'] ?? ''),
            'opis' => sanitize($_POST['opis'] ?? ''),
            'kolejnosc' => !empty($_POST['kolejnosc']) ? intval($_POST['kolejnosc']) : 0,
            'aktywny' => isset($_POST['aktywny']) ? 1 : 0
        ];

        if (empty($data['sciezka_zdjecia'])) {
            $message = 'Ścieżka do zdjęcia jest wymagana.';
            $messageType = 'danger';
        } else {
            if ($post_action === 'add') {
                if ($banerObj->add($data)) {
                    $message = 'Baner dodany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy dodawaniu banera.';
                    $messageType = 'danger';
                }
            } else {
                $banner_id = intval($_POST['banner_id']);
                if ($banerObj->update($banner_id, $data)) {
                    $message = 'Baner zaktualizowany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy aktualizacji banera.';
                    $messageType = 'danger';
                }
            }
        }
    } elseif ($post_action === 'delete' && isset($_POST['banner_id'])) {
        if ($banerObj->delete(intval($_POST['banner_id']))) {
            $message = 'Baner usunięty pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu banera.';
            $messageType = 'danger';
        }
    }
}

$bannerToEdit = null;
if ($subaction === 'edit' && $id > 0) {
    $bannerToEdit = $banerObj->getById($id);
}
?>

<h2><i class="fas fa-image"></i> Zarządzanie banerami</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($subaction === 'add' || $subaction === 'edit'): ?>
    <!-- FORMULARZ -->
    <div class="form-section">
        <h3><?php echo $subaction === 'add' ? 'Dodaj nowy baner' : 'Edytuj baner'; ?></h3>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="<?php echo $subaction; ?>">
            <?php if ($bannerToEdit): ?>
                <input type="hidden" name="banner_id" value="<?php echo $bannerToEdit['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="sciezka_zdjecia">Ścieżka do zdjęcia *</label>
                <input type="text" id="sciezka_zdjecia" name="sciezka_zdjecia" 
                       placeholder="Np. baner1.jpg lub foldery/baner1.jpg"
                       value="<?php echo ($bannerToEdit['sciezka_zdjecia'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="tytul">Tytuł</label>
                <input type="text" id="tytul" name="tytul" 
                       value="<?php echo ($bannerToEdit['tytul'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="opis">Opis</label>
                <textarea id="opis" name="opis"><?php echo ($bannerToEdit['opis'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="kolejnosc">Kolejność wyświetlania</label>
                <input type="number" id="kolejnosc" name="kolejnosc" 
                       value="<?php echo ($bannerToEdit['kolejnosc'] ?? 0); ?>">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="aktywny" value="1" 
                           <?php echo (isset($bannerToEdit['aktywny']) && $bannerToEdit['aktywny']) ? 'checked' : ''; ?>>
                    Baner aktywny (widoczny na stronie)
                </label>
            </div>

            <div class="form-group btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz
                </button>
                <a href="?action=banery" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- LISTA BANERÓW -->
    <div style="margin-bottom: 1rem;">
        <a href="?action=banery&subaction=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj nowy baner
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tytuł</th>
                    <th>Ścieżka zdjęcia</th>
                    <th>Kolejność</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $banersResult = $banerObj->getAll(false);
                    if ($banersResult && $banersResult->num_rows > 0):
                        while ($banner = $banersResult->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo ($banner['tytul'] ?? '-'); ?></td>
                        <td><?php echo ($banner['sciezka_zdjecia']); ?></td>
                        <td><?php echo $banner['kolejnosc']; ?></td>
                        <td>
                            <?php echo $banner['aktywny'] ? '<span style="color: green;"><i class="fas fa-check"></i> Aktywny</span>' : '<span style="color: red;"><i class="fas fa-ban"></i> Nieaktywny</span>'; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=banery&subaction=edit&id=<?php echo $banner['id']; ?>" class="btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edytuj
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten baner?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="banner_id" value="<?php echo $banner['id']; ?>">
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
                        <td colspan="5" style="text-align: center; padding: 2rem;">Brak banerów</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
