<?php
// Strona główna - newsy, baner, menu, widgety
require_once '../database/db_connect.php';
include '../strona/layout/header.php';
?>
<div class="container">
  <div class="panel" id="left-panel">
    <?php include '../strona/widgets.php'; ?>
  </div>
  <div class="panel" id="main-content">
    <?php include '../strona/news.php'; ?>
  </div>
  <div class="panel" id="right-panel">
    <?php include '../strona/menu_anim.php'; ?>
  </div>
</div>
<?php include '../strona/layout/footer.php'; ?>