<?php
/**
 * Strona główna - wyświetlanie newsów/artykułów
 */

require_once __DIR__ . '/../database/News.php';

$newsObj = new News();
$articlesResult = $newsObj->getAll(true);

if ($articlesResult && $articlesResult->num_rows > 0):
?>
    <h1><i class="fas fa-newspaper"></i> Aktualności</h1>
    <p>Zapraszamy do zapoznania się z najnowszymi informacjami z naszej biblioteki.</p>

    <div class="articles-container">
        <?php while ($article = $articlesResult->fetch_assoc()): ?>
            <article class="article">
                <h2 class="article-title"><?php echo htmlspecialchars($article['tytul']); ?></h2>
                <div class="article-date">
                    <i class="fas fa-calendar"></i>
                    <?php 
                        $date = new DateTime($article['data_publikacji']);
                        echo $date->format('d.m.Y H:i');
                    ?>
                </div>

                <?php if ($article['zdjecie']): ?>
                    <img src="<?php echo IMAGES_PATH; ?>/<?php echo htmlspecialchars($article['zdjecie']); ?>" 
                         alt="<?php echo htmlspecialchars($article['tytul']); ?>" 
                         class="article-image">
                <?php endif; ?>

                <div class="article-excerpt">
                    <?php echo htmlspecialchars($article['wstep'] ?? substr($article['tresc'], 0, 200)); ?>
                    ...
                </div>

                <div class="article-footer">
                    <span class="article-author">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($article['autor']); ?>
                    </span>
                    <a href="?page=article&id=<?php echo $article['id']; ?>" class="btn-more">
                        Czytaj więcej <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </article>
        <?php endwhile; ?>
    </div>

    <hr style="margin: 2rem 0; border: none; border-top: 2px solid #ecf0f1;">

    <h2 style="margin-top: 2rem;">Księgozbiór</h2>
    <p>Zapraszamy do przeglądania naszego katalogu książek. Możesz wyszukać interesujące Cię pozycje lub skorzystać z formularza poniżej.</p>
    
    <div style="text-align: center; margin: 2rem 0;">
        <a href="?page=search" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
            <i class="fas fa-search"></i> Przejdź do katalogu
        </a>
    </div>

<?php else: ?>
    <h1>Witaj w Bibliotece Szkolnej!</h1>
    <p>Strona jest w przygotowaniu. Wróć wkrótce!</p>
<?php endif; ?>
