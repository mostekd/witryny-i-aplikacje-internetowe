<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Księga gości - moderacja</h1>
        </div>

        <!-- Lista wpisów do moderacji -->
        <div class="admin-table-container">
            <div class="table-filters">
                <select onchange="filterPosts(this.value)">
                    <option value="pending">Oczekujące</option>
                    <option value="approved">Zatwierdzone</option>
                    <option value="rejected">Odrzucone</option>
                    <option value="all">Wszystkie</option>
                </select>
                <input type="text" id="searchPost" placeholder="Szukaj..." oninput="searchPosts(this.value)">
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nick</th>
                        <th>Email</th>
                        <th>Treść</th>
                        <th>Data dodania</th>
                        <th>Status</th>
                        <th>Akcje</th>
                    </tr>
                </thead>
                <tbody id="guestbookTableBody">
                    <?php
                    $sql = "select * from ksiega_gosci order by data_dodania desc";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                        $status = $row['zatwierdzony'] ? 'approved' : ($row['odrzucony'] ? 'rejected' : 'pending');
                    ?>
                    <tr class="status-<?= $status ?>">
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['nick']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td class="post-content"><?= nl2br(htmlspecialchars($row['tresc'])) ?></td>
                        <td><?= $row['data_dodania'] ?></td>
                        <td>
                            <span class="badge badge-<?= $status ?>">
                                <?php
                                switch($status) {
                                    case 'approved': echo 'Zatwierdzony'; break;
                                    case 'rejected': echo 'Odrzucony'; break;
                                    default: echo 'Oczekuje';
                                }
                                ?>
                            </span>
                        </td>
                        <td class="actions">
                            <?php if (!$row['zatwierdzony'] && !$row['odrzucony']): ?>
                            <button onclick="moderatePost(<?= $row['id'] ?>, 'approve')" class="btn-approve">Zatwierdź</button>
                            <button onclick="moderatePost(<?= $row['id'] ?>, 'reject')" class="btn-reject">Odrzuć</button>
                            <?php else: ?>
                            <button onclick="moderatePost(<?= $row['id'] ?>, 'reset')" class="btn-reset">Reset</button>
                            <?php endif; ?>
                            <button onclick="deletePost(<?= $row['id'] ?>)" class="btn-delete">Usuń</button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script>
function moderatePost(id, action) {
    fetch('api/moderate_post.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id, action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Wystąpił błąd podczas moderacji wpisu');
        }
    });
}

function deletePost(id) {
    if (confirm('Czy na pewno chcesz usunąć ten wpis?')) {
        fetch('api/moderate_post.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id, action: 'delete' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Wystąpił błąd podczas usuwania wpisu');
            }
        });
    }
}

function filterPosts(status) {
    const rows = document.getElementById('guestbookTableBody').getElementsByTagName('tr');
    for (let row of rows) {
        if (status === 'all' || row.classList.contains('status-' + status)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}

function searchPosts(query) {
    query = query.toLowerCase();
    const rows = document.getElementById('guestbookTableBody').getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        if (row.style.display !== 'none' && text.includes(query)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
}
</script>

<?php include 'layout/footer.php'; ?>