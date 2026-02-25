<?php
session_start();
include_once '../model/fonction.php';
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
  <head>
    <meta charset="UTF-8" />
    <title><?php echo ucfirst(str_replace(".php", "", basename($_SERVER['PHP_SELF']))); ?></title>
    <link rel="stylesheet" href="../public/css/style.css" />
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  </head>
  <body>
    <div class="sidebar">
        <div class="sidebar-logo">
         <img src="../public/img/logo-removebg-preview.png" alt="EasyStock_Pro">
        </div>
      <ul class="nav-links">
        <li>
          <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="dashboard.php"? "active":""; ?>">
            <i class="bx bx-grid-alt"></i>
            <span class="links_name">Tableau de bord</span>
          </a>
        </li>
        <li>
          <a href="vente.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="vente.php"? "active":""; ?>">
            <i class="fa-solid fa-cart-shopping"></i>
            <span class="links_name">Ventes</span>
          </a>
        </li>
        <?php if (peutGererStock()): ?>
        <li>
            <a href="stock.php" class="<?= basename($_SERVER['PHP_SELF']) == "stock.php" ? "active" : "" ?>">
                <i class="bx bx-coin-stack"></i>
                <span class="links_name">Stock</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
        <a href="vente_liste.php" class="<?= basename($_SERVER['PHP_SELF']) == "vente_liste.php" ? "active" : "" ?>">
            <i class="bx bx-history"></i>
            <span class="links_name">Historique ventes</span>
        </a>
        </li>
        <?php if (peutGererStock()): ?>
        <li>
          <a href="article.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="article.php"? "active":""; ?>">
            <i class="bx bx-box"></i>
            <span class="links_name">Articles</span>
          </a>
        </li>
        <li>
          <a href="categorie.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="categorie.php"? "active":""; ?>">
            <i class="fa-solid fa-shop"></i>
            <span class="links_name">Categorie</span>
          </a>
        </li>
        <li>
          <a href="fournisseur.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="fournisseur.php"? "active":""; ?>">
            <i class="bx bx-user"></i>
            <span class="links_name">Fournisseurs</span>
          </a>
        </li>
        <li>
          <a href="commande.php" class="<?php echo basename($_SERVER['PHP_SELF'])=="commande.php"? "active":""; ?>">
            <i class="bx bx-list-ul"></i>
            <span class="links_name">Commandes</span>
          </a>
        </li>
        <li>
            <a href="historique_stock.php" class="<?= basename($_SERVER['PHP_SELF']) == "historique_stock.php" ? "active" : "" ?>">
                <i class="bx bx-history"></i>
                <span class="links_name">Historique</span>
            </a>
        </li>
        <?php endif; ?>
        <?php if (peutGererStock()): ?>
        <li>
          <a href="client.php"  class="<?php echo basename($_SERVER['PHP_SELF'])=="client.php"? "active":""; ?>">
            <i class="bx bx-user"></i>
            <span class="links_name">Clients</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if (estAdmin()): ?>
        <li>
            <a href="utilisateur.php" class="<?= basename($_SERVER['PHP_SELF']) == "utilisateur.php" ? "active" : "" ?>">
                <i class="bx bx-user"></i>
                <span class="links_name">Utilisateurs</span>
            </a>
        </li>
        <li>
            <a href="configuration.php" class="<?= basename($_SERVER['PHP_SELF']) == "configuration.php" ? "active" : "" ?>">
                <i class="bx bx-cog"></i>
                <span class="links_name">Configuration</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="profil.php" class="<?= basename($_SERVER['PHP_SELF']) == "profil.php" ? "active" : "" ?>">
                <i class="bx bx-user-circle"></i>
                <span class="links_name">Mon profil</span>
            </a>
        </li>
        <li class="log_out">
            <a href="logout.php"class=" <?= basename($_SERVER['PHP_SELF']) == "logout.php" ? "active" : "" ?>">
                <i class="bx bx-log-out"></i>
                <span class="links_name">Deconnexion</span>
            </a>
        </li>
      </ul>
    </div>
    <section class="home-section">
    <nav>
    <div class="sidebar-button">
        <i class="bx bx-menu" id="sidebarToggle"></i>
        <span class="dashboard"><?= ucfirst(str_replace(".php", "", basename($_SERVER['PHP_SELF']))) ?></span>
    </div>
    <div class="profile-details">
        <div class="user-info">
            <span class="user-name">
                <?= $_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom'] ?>
            </span>
            <span class="user-role" style="font-size:12px; padding:3px 10px; border-radius:20px; background:<?= $_SESSION['user']['role'] == 'admin' ? '#f44336' : ($_SESSION['user']['role'] == 'gestionnaire' ? '#2196f3' : '#ff9800') ?>; color:white; margin-left:10px;">
                <?= strtoupper($_SESSION['user']['role']) ?>
            </span>
        </div> 
    </div>
    </nav>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<script>
(function() {
    function initSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar || !toggleBtn || !overlay) {
            setTimeout(initSidebar, 500);
            return;
        }
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
        sidebar.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSidebar);
    } else {
        initSidebar();
    }
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

</body>
</html>