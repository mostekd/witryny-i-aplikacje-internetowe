<?php
/**
 * Zarządzanie księgą gości - zatwierdzanie wpisów
 */

$message = '';
$messageType = '';

// Obsługa akcji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $post_action = sanitize($_POST['action']);
    
    if ($post_action === 'approve' && isset($_POST['pending_id'])) {
        if ($ksiegaObj->approve(intval($_POST['pending_id']))) {
            $message = 'Wpis zatwierdzony i opublikowany.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy zatwierdzaniu wpisu.';
            $messageType = 'danger';
        }
    } elseif ($post_action === 'reject' && isset($_POST['pending_id'])) {
        if ($ksiegaObj->reject(intval($_POST['pending_id']))) {
            $message = 'Wpis odrzucony i usunięty.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy odrzucaniu wpisu.';
            $messageType = 'danger';
        }
    } elseif ($post_action === 'delete' && isset($_POST['published_id'])) {
        if ($ksiegaObj->delete(intval($_POST['published_id']))) {
            $message = 'Wpis usunięty z księgi gości.';
            $messageType = 'success';
        } else {
            $message = 'Błąd przy usuwaniu wpisu.';
            $messageType = 'danger';
        }
    }
}
?>

<h2><i class="fas fa-comments"></i> Księga gości</h2>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas fa-<?php echo $messageType === 'success' ? 'check' : 'exclamation'; ?>-circle"></i>
        <?php echo $message; ?>
    </div>
<?php endif; ?>

<h3>Wpisy oczekujące na zatwierdzenie</h3>

<?php 
    $pendingResult = $ksiegaObj->getPending();
    if ($pendingResult && $pendingResult->num_rows > 0):
?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nick</th>
                    <th>E-mail</th>
                    <th>Treść</th>
                    <th>Data</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($entry = $pendingResult->fetch_assoc()): ?>
                    <tr style="background-color: #fff3cd;">
                        <td><?php echo htmlspecialchars($entry['nick']); ?></td>
                        <td><?php echo htmlspecialchars($entry['email']); ?></td>
                        <td><?php echo htmlspecialchars(substr($entry['tresc'], 0, 100) . '...'); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($entry['data_dodania'])); ?></td>
                        <td>
                            <div class="action-buttons" style="flex-direction: column;">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="pending_id" value="<?php echo $entry['id']; ?>">
                                    <button type="submit" class="btn-sm btn-approve">
                                        <i class="fas fa-check"></i> Zatwierdź
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="pending_id" value="<?php echo $entry['id']; ?>">
                                    <button type="submit" class="btn-sm btn-reject">
                                        <i class="fas fa-times"></i> Odrzuć
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Brak wpisów do zatwierdzenia - wszystkie czeka na przesłanie!
    </div>
<?php endif; ?>

<hr style="margin: 2rem 0;">

<h3>Opublikowane wpisy</h3>

<?php 
    $publishedResult = $ksiegaObj->getAll();
    if ($publishedResult && $publishedResult->num_rows > 0):
?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nick</th>
                    <th>E-mail</th>
                    <th>Treść</th>
                    <th>Data publikacji</th>
                    <th>Akcje</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($entry = $publishedResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($entry['nick']); ?></td>
                        <td><?php echo htmlspecialchars($entry['email']); ?></td>
                        <td><?php echo htmlspecialchars(substr($entry['tresc'], 0, 100) . '...'); ?></td>
                        <td><?php echo date('d.m.Y H:i', strtotime($entry['data_dodania'])); ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Czy na pewno chcesz usunąć ten wpis?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="published_id" value="<?php echo $entry['id']; ?>">
                                <button type="submit" class="btn-sm btn-delete">
                                    <i class="fas fa-trash"></i> Usuń
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Brak opublikowanych wpisów.
    </div>
<?php endif; ?>
