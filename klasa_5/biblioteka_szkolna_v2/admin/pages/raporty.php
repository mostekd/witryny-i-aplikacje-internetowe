<?php
/**
 * Raporty - zaawansowana analiza
 */

$report_type = isset($_GET['report']) ? sanitize($_GET['report']) : '';
$dias_threshold = isset($_POST['dias_threshold']) ? intval($_POST['dias_threshold']) : DEFAULT_LOAN_PERIOD;
$search_litera = isset($_POST['search_litera']) ? sanitize($_POST['search_litera']) : '';
?>

<h2><i class="fas fa-chart-bar"></i> Raporty</h2>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <a href="?action=raporty&report=overdue" class="dashboard-card" style="text-decoration: none; cursor: pointer;">
        <h3><i class="fas fa-exclamation-triangle"></i> Książki przetrzymywane</h3>
        <p>Raport książek nie zwróconych powyżej określonego okresu</p>
    </a>

    <a href="?action=raporty&report=students" class="dashboard-card" style="text-decoration: none; cursor: pointer;">
        <h3><i class="fas fa-graduation-cap"></i> Uczniowie i wypożyczenia</h3>
        <p>Raport aktywności uczniów</p>
    </a>

    <a href="?action=raporty&report=books" class="dashboard-card" style="text-decoration: none; cursor: pointer;">
        <h3><i class="fas fa-book"></i> Statystyka książek</h3>
        <p>Raport dostępności i popularności</p>
    </a>
</div>

<hr style="margin: 2rem 0;">

<?php if ($report_type === 'overdue'): ?>
    <h3>Książki przetrzymywane</h3>
    <p>Książki nie zwrócone powyżej <?php echo DEFAULT_LOAN_PERIOD; ?> dni</p>

    <div class="form-section" style="margin-bottom: 2rem;">
        <form method="POST" class="form" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="dias_threshold">Próg alertu (dni):</label>
                <input type="number" id="dias_threshold" name="dias_threshold" 
                       min="1" value="<?php echo $dias_threshold; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filtruj</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Książka</th>
                    <th>Uczeń</th>
                    <th>Wypożyczona</th>
                    <th>Powinno być zwrócone</th>
                    <th>Dni przekroczenia</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $overdueResult = $wypozyczeniaObj->getOverdueBooks($dias_threshold);
                    if ($overdueResult && $overdueResult->num_rows > 0):
                        while ($book = $overdueResult->fetch_assoc()):
                ?>
                    <tr style="background-color: #ffebee;">
                        <td><?php echo ($book['tytul']); ?></td>
                        <td><?php echo ($book['imie'] . ' ' . $book['nazwisko']); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($book['data_wypozyczenia'])); ?></td>
                        <td><?php echo date('d.m.Y', strtotime($book['data_planowanego_zwrotu'])); ?></td>
                        <td style="font-weight: bold; color: red;"><?php echo $book['dni_przekroczenia']; ?> dni</td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">Brak przetrzymanych książek</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($report_type === 'students'): ?>
    <h3>Aktywność uczniów</h3>
    <p>Raport wypożyczeń i aktywności poszczególnych uczniów</p>

    <div class="form-section" style="margin-bottom: 2rem;">
        <form method="POST" class="form" style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="search_litera">Szukaj po nazwisku (pierwsza litera):</label>
                <input type="text" id="search_litera" name="search_litera" 
                       maxlength="1" placeholder="Np. K"
                       value="<?php echo ($search_litera); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Szukaj</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Uczeń</th>
                    <th>Klasa</th>
                    <th>Aktywne wypożyczenia</th>
                    <th>Łącznie wypożyczył</th>
                    <th>E-mail</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!empty($search_litera)) {
                        $studentsResult = $uczenObj->search(strtolower($search_litera), '', '', true);
                    } else {
                        $studentsResult = $uczenObj->getAll(true);
                    }
                    
                    if ($studentsResult && $studentsResult->num_rows > 0):
                        while ($student = $studentsResult->fetch_assoc()):
                            $activeRentals = $uczenObj->getAktywneWypozyczenia($student['id']);
                            $allRentals = $uczenObj->getWypozyczenia($student['id']);
                            $activeCount = $activeRentals ? $activeRentals->num_rows : 0;
                            $allCount = $allRentals ? $allRentals->num_rows : 0;
                ?>
                    <tr>
                        <td><?php echo ($student['imie'] . ' ' . $student['nazwisko']); ?></td>
                        <td><?php echo ($student['klasa'] ?? '-'); ?></td>
                        <td><?php echo $activeCount; ?></td>
                        <td><?php echo $allCount; ?></td>
                        <td><?php echo ($student['email'] ?? '-'); ?></td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem;">Brak danych</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($report_type === 'books'): ?>
    <h3>Statystyka książek</h3>
    <p>Raport dostępności i wykorzystania księgozbioru</p>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Książka</th>
                    <th>Autor</th>
                    <th>Kopie</th>
                    <th>Dostępne</th>
                    <th>Wypożyczone</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $booksResult = $ksiazkaObj->getAll(true);
                    if ($booksResult && $booksResult->num_rows > 0):
                        while ($book = $booksResult->fetch_assoc()):
                            $available = $ksiazkaObj->getAvailableCopies($book['id']);
                            $borrowed = $book['ilosc_kopii'] - $available;
                ?>
                    <tr>
                        <td><?php echo ($book['tytul']); ?></td>
                        <td><?php echo ($book['autor']); ?></td>
                        <td><?php echo $book['ilosc_kopii']; ?></td>
                        <td><?php echo $available; ?></td>
                        <td><?php echo $borrowed; ?></td>
                        <td>
                            <?php 
                                if ($available > 0) {
                                    echo '<span style="color: green;"><i class="fas fa-check"></i> Dostępna</span>';
                                } else {
                                    echo '<span style="color: red;"><i class="fas fa-times"></i> Niedostępna</span>';
                                }
                            ?>
                        </td>
                    </tr>
                <?php
                        endwhile;
                    else:
                ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem;">Brak książek</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Wybierz typ raportu z listy powyżej.
    </div>
<?php endif; ?>
