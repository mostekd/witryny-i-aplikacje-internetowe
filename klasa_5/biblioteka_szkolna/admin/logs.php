<?php
session_start();
require_once '../database/db_connect.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

include 'layout/header.php';
?>

<div class="admin-container">
    <?php include 'layout/sidebar.php'; ?>
    
    <main class="admin-main">
        <div class="admin-header">
            <h1>Logi administracyjne</h1>
        </div>

        <!-- Filtry -->
        <div class="logs-filters">
            <div class="filter-group">
                <label>Administrator:</label>
                <select id="adminFilter">
                    <option value="">Wszyscy</option>
                    <?php
                    $result = $conn->query("select distinct a.id, a.login 
                                          from administrator a 
                                          join logi_admin l on a.id = l.admin_id 
                                          order by a.login");
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['login']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Data od:</label>
                <input type="date" id="dateFrom" value="<?= date('Y-m-d', strtotime('-7 days')) ?>">
            </div>
            
            <div class="filter-group">
                <label>Data do:</label>
                <input type="date" id="dateTo" value="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="filter-group">
                <label>Szukaj:</label>
                <input type="text" id="searchLog" placeholder="Szukaj w akcjach...">
            </div>
            
            <button onclick="applyFilters()" class="btn-filter">Filtruj</button>
        </div>

        <!-- Lista logów -->
        <div class="logs-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Administrator</th>
                        <th>Akcja</th>
                        <th>Data operacji</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <?php
                    $sql = "select l.*, a.login 
                           from logi_admin l 
                           join administrator a on l.admin_id = a.id 
                           order by l.data_operacji desc 
                           limit 100";
                    $result = $conn->query($sql);
                    while ($row = $result->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['login']) ?></td>
                        <td><?= htmlspecialchars($row['akcja']) ?></td>
                        <td><?= $row['data_operacji'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Eksport -->
        <div class="export-section">
            <button onclick="exportLogs('csv')" class="btn-export">Eksportuj do CSV</button>
            <button onclick="exportLogs('pdf')" class="btn-export">Eksportuj do PDF</button>
        </div>
    </main>
</div>

<script>
function applyFilters() {
    const adminId = document.getElementById('adminFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const search = document.getElementById('searchLog').value;

    fetch('api/get_logs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            adminId,
            dateFrom,
            dateTo,
            search
        })
    })
    .then(response => response.json())
    .then(data => {
        const tbody = document.getElementById('logsTableBody');
        tbody.innerHTML = '';
        
        data.forEach(log => {
            tbody.innerHTML += `
                <tr>
                    <td>${log.id}</td>
                    <td>${escapeHtml(log.login)}</td>
                    <td>${escapeHtml(log.akcja)}</td>
                    <td>${log.data_operacji}</td>
                </tr>
            `;
        });
    });
}

function exportLogs(format) {
    const adminId = document.getElementById('adminFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const search = document.getElementById('searchLog').value;

    window.location.href = `api/export_logs.php?format=${format}&adminId=${adminId}&dateFrom=${dateFrom}&dateTo=${dateTo}&search=${encodeURIComponent(search)}`;
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Automatyczne odświeżanie co 30 sekund
setInterval(applyFilters, 30000);
</script>

<style>
.logs-filters {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    min-width: 200px;
}

.filter-group label {
    margin-bottom: 0.5rem;
    color: #666;
}

.filter-group input,
.filter-group select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-filter {
    align-self: flex-end;
    padding: 0.5rem 1rem;
    background: #2196F3;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.logs-container {
    margin: 1rem 0;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: auto;
}

.export-section {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}

.btn-export {
    padding: 0.5rem 1rem;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-filter:hover,
.btn-export:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .logs-filters {
        flex-direction: column;
    }
    
    .filter-group {
        width: 100%;
    }
    
    .btn-filter {
        width: 100%;
        margin-top: 1rem;
    }
}
</style>

<?php include 'layout/footer.php'; ?>