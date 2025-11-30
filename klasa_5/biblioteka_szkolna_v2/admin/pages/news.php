<?php
/**
 * Zarządzanie artykułami/newsami
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
            'tytul' => sanitize($_POST['tytul'] ?? ''),
            'wstep' => sanitize($_POST['wstep'] ?? ''),
            'tresc' => sanitize($_POST['tresc'] ?? ''),
            'autor' => sanitize($_POST['autor'] ?? ''),
            'zdjecie' => sanitize($_POST['zdjecie'] ?? ''),
            'opublikowany' => isset($_POST['opublikowany']) ? 1 : 0
        ];

        if (empty($data['tytul']) || empty($data['tresc'])) {
            $message = 'Tytuł i treść artykułu są wymagane.';
            $messageType = 'danger';
        } else {
            if (empty($data['autor'])) {
                $data['autor'] = $_SESSION['admin_imie'] . ' ' . $_SESSION['admin_nazwisko'];
            }

            if ($post_action === 'add') {
                if ($newsObj->add($data)) {
                    $message = 'Artykuł dodany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy dodawaniu artykułu.';
                    $messageType = 'danger';
                }
            } else {
                $article_id = intval($_POST['article_id']);
                if ($newsObj->update($article_id, $data)) {
                    $message = 'Artykuł zaktualizowany pomyślnie.';
                    $messageType = 'success';
                } else {
                    $message = 'Błąd przy aktualizacji artykułu.';
                    $messageType = 'danger';
                }
            }
        }
    } elseif ($post_action === 'delete' && isset($_POST['article_id'])) {
        if ($newsObj->delete(intval($_POST['article_id']))) {
            $message = 'Artykuł usunięty pomyślnie.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu artykułu.';
            $messageType = 'danger';
        }
    }
}

$articleToEdit = null;
if ($subaction === 'edit' && $id > 0) {
    $articleToEdit = $newsObj->getByIdAdmin($id);
}
?>

<h2><i class="fas fa-newspaper"></i> Zarządzanie artykułami</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<?php if ($subaction === 'add' || $subaction === 'edit'): ?>
    <!-- FORMULARZ -->
    <div class="form-section">
        <h3><?php echo $subaction === 'add' ? 'Dodaj nowy artykuł' : 'Edytuj artykuł'; ?></h3>
        <form method="POST" class="form">
            <input type="hidden" name="action" value="<?php echo $subaction; ?>">
            <?php if ($articleToEdit): ?>
                <input type="hidden" name="article_id" value="<?php echo $articleToEdit['id']; ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="tytul">Tytuł *</label>
                <input type="text" id="tytul" name="tytul" 
                       value="<?php echo ($articleToEdit['tytul'] ?? ''); ?>"
                       required>
            </div>

            <div class="form-group">
                <label for="wstep">Wstęp (skrót)</label>
                <input type="text" id="wstep" name="wstep" 
                       placeholder="Krótka zapowiedź artykułu"
                       value="<?php echo ($articleToEdit['wstep'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="tresc">Treść *</label>
                <textarea id="tresc" name="tresc" required><?php echo ($articleToEdit['tresc'] ?? ''); ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="autor">Autor</label>
                    <input type="text" id="autor" name="autor" 
                           placeholder="Pozostaw puste, aby ustawić Twoje dane"
                           value="<?php echo ($articleToEdit['autor'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="zdjecie">Zdjęcie (nazwa pliku)</label>
                    <input type="text" id="zdjecie" name="zdjecie" 
                           placeholder="Np. artykul1.jpg"
                           value="<?php echo ($articleToEdit['zdjecie'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="opublikowany" value="1" 
                           <?php echo (isset($articleToEdit['opublikowany']) && $articleToEdit['opublikowany']) ? 'checked' : ''; ?>>
                    Opublikuj artykuł (widoczny na stronie głównej)
                </label>
            </div>

            <div class="form-group btn-group">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Zapisz
                </button>
                <a href="?action=news" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Anuluj
                </a>
            </div>
        </form>
    </div>

<?php else: ?>
    <!-- LISTA ARTYKUŁÓW -->
    <div style="margin-bottom: 1rem;">
        <a href="?action=news&subaction=add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Dodaj nowy artykuł
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tytuł</th>
                    <th>Autor</th>
                    <th>Data publikacji</th>
                    <th>Wyświetlenia</th>
                    <th>Status</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $newsResult = $newsObj->getAll(false);
                    if ($newsResult && $newsResult->num_rows > 0):
                        while ($article = $newsResult->fetch_assoc()):
                ?>
                    <tr>
                        <td><?php echo ($article['tytul']); ?></td>
                        <td><?php echo ($article['autor']); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($article['data_publikacji'])); ?></td>
                        <td><?php echo $article['ilosc_wyswietlen']; ?></td>
                        <td>
                            <?php echo $article['opublikowany'] ? '<span style="color: green;"><i class="fas fa-check"></i> Opublikowany</span>' : '<span style="color: red;"><i class="fas fa-ban"></i> Szkic</span>'; ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="?action=news&subaction=edit&id=<?php echo $article['id']; ?>" class="btn-sm btn-edit">
                                    <i class="fas fa-edit"></i> Edytuj
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten artykuł?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
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
                        <td colspan="6" style="text-align: center; padding: 2rem;">Brak artykułów</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
