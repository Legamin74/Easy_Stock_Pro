<?php
include 'entete.php';
?>

<div class="home-content" style="display: flex; align-items: center; justify-content: center; min-height: 60vh;">
    <div style="text-align: center; max-width: 500px;">
        <div style="font-size: 80px; color: #f44336; margin-bottom: 20px;">
            <i class="bx bx-shield-x"></i>
        </div>
        <h1 style="color: #333; margin-bottom: 15px;"> Accès refusé</h1>
        <p style="color: #666; margin-bottom: 25px; font-size: 16px;">
            <?= $_SESSION['erreur'] ?? "Vous n'avez pas les permissions nécessaires pour accéder à cette page." ?>
        </p>
        <a href="dashboard.php" class="btn-submit" style="display: inline-block; width: auto; padding: 12px 30px; text-decoration: none;">
            <i class="bx bx-home"></i>
            Retour au tableau de bord
        </a>
    </div>
</div>

<?php 
unset($_SESSION['erreur']);
include 'pied.php'; 
?>