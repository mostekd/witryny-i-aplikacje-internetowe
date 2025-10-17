<?php
session_start();
require_once '../database/db_connect.php';

// Sprawdzenie czy administrator jest zalogowany
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Pobranie informacji o zalogowanym administratorze
$admin_id = $_SESSION['admin_id'];
$sql = "select a.*, GROUP_CONCAT(r.nazwa) as role 
        from administrator a 
        left join administrator_role ar on a.id = ar.administrator_id
        left join role r on ar.rola_id = r.id
        where a.id = $admin_id
        group by a.id";
$admin = $conn->query($sql)->fetch_assoc();

echo '<link rel="stylesheet" href="../css/style_admin.css">';
include 'layout/header.php';
?>

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="admin-profile">
            <div class="admin-avatar">👤</div>
            <div class="admin-info">
                <div class="admin-name"><?= htmlspecialchars($admin['login']) ?></div>
                <div class="admin-roles"><?= htmlspecialchars($admin['role']) ?></div>
            </div>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="active">Dashboard</a>
            <a href="news.php">Zarządzanie newsami</a>
            <a href="books.php">Zarządzanie książkami</a>
            <a href="students.php">Zarządzanie uczniami</a>
            <a href="loans.php">Wypożyczenia</a>
            <a href="guestbook.php">Księga gości</a>
            <a href="reports.php">Raporty</a>
            <a href="settings.php">Ustawienia</a>
            <a href="logout.php">Wyloguj</a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <h1>Panel administracyjny</h1>
        
        <div class="dashboard-grid">
            <!-- Statystyki -->
            <div class="dashboard-card">
                <h3>Statystyki</h3>
                <?php
                $stats = [];
                $stats['books'] = $conn->query("select count(*) as cnt from ksiazki")->fetch_assoc()['cnt'];
                $stats['active_loans'] = $conn->query("select count(*) as cnt from wypozyczenia where status='wypozyczona'")->fetch_assoc()['cnt'];
                $stats['overdue'] = $conn->query("select count(*) as cnt from wypozyczenia where status='przeterminowana'")->fetch_assoc()['cnt'];
                $stats['pending_posts'] = $conn->query("select count(*) as cnt from ksiega_gosci where zatwierdzony=0 and odrzucony=0")->fetch_assoc()['cnt'];
                ?>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['books'] ?></div>
                        <div class="stat-label">Książek w bazie</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['active_loans'] ?></div>
                        <div class="stat-label">Aktywnych wypożyczeń</div>
                    </div>
                    <div class="stat-item warning">
                        <div class="stat-value"><?= $stats['overdue'] ?></div>
                        <div class="stat-label">Przeterminowanych</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value"><?= $stats['pending_posts'] ?></div>
                        <div class="stat-label">Wpisów do moderacji</div>
                    </div>
                </div>
            </div>

            <!-- Ostatnie wypożyczenia -->
            <div class="dashboard-card">
                <h3>Ostatnie wypożyczenia</h3>
                <?php
                $sql = "select w.*, k.tytul, u.imie, u.nazwisko, u.klasa
                        from wypozyczenia w
                        join ksiazki k on w.ksiazka_id = k.id
                        join uczniowie u on w.uczen_id = u.id
                        order by w.data_wypozyczenia desc limit 5";
                $result = $conn->query($sql);
                ?>
                <table class="admin-table">
                    <tr>
                        <th>Data</th>
                        <th>Uczeń</th>
                        <th>Książka</th>
                        <th>Status</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['data_wypozyczenia']) ?></td>
                        <td><?= htmlspecialchars($row['imie'] . ' ' . $row['nazwisko']) ?> (<?= htmlspecialchars($row['klasa']) ?>)</td>
                        <td><?= htmlspecialchars($row['tytul']) ?></td>
                        <td><span class="status-<?= $row['status'] ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
            </div>

            <!-- Wpisy do moderacji -->
            <div class="dashboard-card">
                <h3>Wpisy do moderacji</h3>
                <?php
                $sql = "select * from ksiega_gosci 
                        where zatwierdzony=0 and odrzucony=0 
                        order by data_dodania desc limit 5";
                $result = $conn->query($sql);
                if ($result->num_rows > 0):
                ?>
                <div class="pending-posts">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="pending-post">
                        <div class="post-header">
                            <span class="post-author"><?= htmlspecialchars($row['nick']) ?></span>
                            <span class="post-date"><?= htmlspecialchars($row['data_dodania']) ?></span>
                        </div>
                        <div class="post-content"><?= nl2br(htmlspecialchars($row['tresc'])) ?></div>
                        <div class="post-actions">
                            <button onclick="moderatePost(<?= $row['id'] ?>, 'approve')" class="btn-approve">Zatwierdź</button>
                            <button onclick="moderatePost(<?= $row['id'] ?>, 'reject')" class="btn-reject">Odrzuć</button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p>Brak wpisów do moderacji.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<script>
function moderatePost(id, action) {
    fetch('api/moderate_post.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({id, action})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>

<?php include 'layout/footer.php'; ?>