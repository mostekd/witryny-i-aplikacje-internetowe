<?php
/**
 * Strona artykułu (pełny widok)
 */

require_once __DIR__ . '/../database/News.php';

$newsObj = new News();
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $article = $newsObj->getById($id);
    
    if ($article) {
        // Zwiększenie licznika wyświetleń
        $newsObj->incrementViews($id);
        ?>
        <div>
            <a href="?page=home" class="btn btn-secondary" style="margin-bottom: 1rem;">
                <i class="fas fa-arrow-left"></i> Powrót do artykułów
            </a>
        </div>

        <article class="article">
            <h1><?php echo htmlspecialchars($article['tytul']); ?></h1>
            
            <div class="article-date" style="margin: 1rem 0;">
                <i class="fas fa-calendar"></i>
                <?php 
                    $date = new DateTime($article['data_publikacji']);
                    echo $date->format('d.m.Y H:i');
                ?>
                | <i class="fas fa-user"></i> <?php echo htmlspecialchars($article['autor']); ?>
                | <i class="fas fa-eye"></i> <?php echo $article['ilosc_wyswietlen']; ?> wyświetleń
            </div>

            <?php if ($article['zdjecie']): ?>
                <img src="<?php echo IMAGES_PATH; ?>/<?php echo htmlspecialchars($article['zdjecie']); ?>" 
                     alt="<?php echo htmlspecialchars($article['tytul']); ?>" 
                     class="article-image" style="max-height: 500px;">
            <?php endif; ?>

            <div style="margin: 2rem 0; line-height: 1.8; color: #555; font-size: 1.05rem;">
                <?php echo nl2br(htmlspecialchars($article['tresc'])); ?>
            </div>
        </article>

        <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ecf0f1;">

        <div style="text-align: center;">
            <a href="?page=home" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Powrót do listy artykułów
            </a>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> Artykuł nie został znaleziony.
        </div>
        <a href="?page=home" class="btn btn-primary">Powrót do strony głównej</a>
        <?php
    }
} else {
    ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> Błędny identyfikator artykułu.
    </div>
    <a href="?page=home" class="btn btn-primary">Powrót do strony głównej</a>
    <?php
}
?>
