<?php
session_start();
require_once '../database/models/loan.php';
$loan = new Loan();

$student_id = $_SESSION['student_id'] ?? 0;
$loans = $loan->get_loans_by_student($student_id);
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title>Moje wypożyczenia</title>
        <link rel="stylesheet" href="../static/css/user.css">
    </head>
    <body>
        <header>
        <h1>Twoje wypożyczenia</h1>
        </header>
        <main>
        <?php if (empty($loans)): ?>
            <p>Brak aktywnych wypożyczeń.</p>
        <?php else: ?>
            <table border="1" cellpadding="5">
            <tr><th>Tytuł</th><th>Data wypożyczenia</th><th>Termin zwrotu</th><th>Status</th></tr>
            <?php foreach ($loans as $l): ?>
            <tr>
                <td><?= htmlspecialchars($l['title']) ?></td>
                <td><?= htmlspecialchars($l['date_borrowed']) ?></td>
                <td><?= htmlspecialchars($l['date_due']) ?></td>
                <td><?= $l['returned'] ? "Zwrócona" : "Wypożyczona" ?></td>
            </tr>
            <?php endforeach; ?>
            </table>
        <?php endif; ?>
        </main>
    </body>
</html>
