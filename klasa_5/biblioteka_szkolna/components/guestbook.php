<?php
// Komponent wyświetlający księgę gości na stronie głównej
require_once 'database/db_connect.php';

// Pobieranie tylko zatwierdzonych wpisów
$sql = "select * from ksiega_gosci 
        where zatwierdzony = 1 
        order by data_dodania desc 
        limit 10";
$result = $conn->query($sql);

if ($result->num_rows > 0):
?>
<section class="guestbook-section">
    <h2>Księga gości</h2>
    <div class="guestbook-entries">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="guestbook-entry">
            <div class="entry-header">
                <span class="entry-author"><?= htmlspecialchars($row['nick']) ?></span>
                <span class="entry-date"><?= date('d.m.Y H:i', strtotime($row['data_dodania'])) ?></span>
            </div>
            <div class="entry-content">
                <?= nl2br(htmlspecialchars($row['tresc'])) ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="guestbook-form">
        <h3>Dodaj wpis</h3>
        <form action="actions/add_guestbook_entry.php" method="post">
            <div class="form-group">
                <label for="nick">Nick:</label>
                <input type="text" id="nick" name="nick" required maxlength="50">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required maxlength="100">
            </div>
            <div class="form-group">
                <label for="tresc">Treść wpisu:</label>
                <textarea id="tresc" name="tresc" required maxlength="1000" rows="4"></textarea>
            </div>
            <button type="submit" class="btn-submit">Dodaj wpis</button>
        </form>
    </div>
</section>

<style>
.guestbook-section {
    margin: 2rem 0;
    padding: 1rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.guestbook-entries {
    margin-bottom: 2rem;
}

.guestbook-entry {
    padding: 1rem;
    border-bottom: 1px solid #eee;
}

.entry-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.entry-author {
    font-weight: bold;
    color: #2196F3;
}

.entry-date {
    color: #666;
    font-size: 0.9rem;
}

.entry-content {
    line-height: 1.5;
    color: #333;
}

.guestbook-form {
    background: #f5f5f5;
    padding: 1rem;
    border-radius: 4px;
}

.guestbook-form h3 {
    margin-bottom: 1rem;
    color: #333;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #666;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
}

.btn-submit {
    background: #2196F3;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.btn-submit:hover {
    background: #1976D2;
}
</style>
<?php endif; ?>