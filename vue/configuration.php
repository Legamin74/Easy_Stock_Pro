<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAdmin(); //  ADMIN UNIQUEMENT

//  TRAITEMENT SAUVEGARDE CONFIGURATION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_config'])) {
    setConfig('entreprise_nom', $_POST['entreprise_nom']);
    setConfig('entreprise_email', $_POST['entreprise_email']);
    setConfig('entreprise_telephone', $_POST['entreprise_telephone']);
    setConfig('entreprise_adresse', $_POST['entreprise_adresse']);
    setConfig('devise', $_POST['devise']);
    setConfig('seuil_alerte_global', $_POST['seuil_alerte_global']);
    setConfig('format_recu', $_POST['format_recu']);
    
    $_SESSION['message'] = [
        'text' => ' Configuration sauvegardée avec succès',
        'type' => 'success'
    ];
    
    echo '<script>window.location.href = "configuration.php";</script>';
    exit;
}
?>

<div class="home-content">
    <!-- ======================================== -->
    <!-- EN-TÊTE PAGE CONFIGURATION              -->
    <!-- ======================================== -->
    <div class="config-header">
        <div class="header-title">
            <i class="bx bx-cog"></i>
            <h2>Configuration</h2>
        </div>
        <span class="badge-role admin">
            <i class="bx bx-shield"></i> Administration
        </span>
    </div>

    <!-- ======================================== -->
    <!-- MESSAGES DE NOTIFICATION                -->
    <!-- ======================================== -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['message']['type'] ?>">
            <i class="bx bx-check-circle"></i>
            <?= $_SESSION['message']['text'] ?>
        </div>
    <?php unset($_SESSION['message']); endif; ?>

    <!-- ======================================== -->
    <!-- FORMULAIRE CONFIGURATION                -->
    <!-- ======================================== -->
    <form method="POST" class="config-form">
        <input type="hidden" name="save_config" value="1">

        <!--  SECTION ENTREPRISE -->
        <div class="config-card">
            <div class="card-header">
                <i class="bx bx-building"></i>
                <h3>Informations entreprise</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Nom de l'entreprise</label>
                    <input type="text" name="entreprise_nom" 
                           value="<?= htmlspecialchars(getConfig('entreprise_nom', 'EasyStock_Pro')) ?>" 
                           placeholder="EasyStock_Pro" required>
                </div>
                
                <div class="form-group">
                    <label>Email de contact</label>
                    <input type="email" name="entreprise_email" 
                           value="<?= htmlspecialchars(getConfig('entreprise_email', 'contact@easystock-pro.com')) ?>"
                           placeholder="contact@easystock.com">
                </div>
                
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="entreprise_telephone" 
                           value="<?= htmlspecialchars(getConfig('entreprise_telephone', '77 123 45 67')) ?>"
                           placeholder="77 123 45 67">
                </div>
                
                <div class="form-group full-width">
                    <label>Adresse</label>
                    <textarea name="entreprise_adresse" rows="2" 
                              placeholder="Dakar, Sénégal"><?= htmlspecialchars(getConfig('entreprise_adresse', 'Dakar, Sénégal')) ?></textarea>
                </div>
            </div>
        </div>

        <!--  SECTION STOCK -->
        <div class="config-card">
            <div class="card-header">
                <i class="bx bx-package"></i>
                <h3>Paramètres stock</h3>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label>Seuil d'alerte global</label>
                    <input type="number" name="seuil_alerte_global" 
                           value="<?= getConfig('seuil_alerte_global', '5') ?>" 
                           min="1" max="100">
                    <small> Stock faible en dessous de ce seuil</small>
                </div>
                
                <div class="form-group">
                    <label>Devise</label>
                    <select name="devise">
                        <option value="FCFA" <?= getConfig('devise', 'FCFA') == 'FCFA' ? 'selected' : '' ?>>FCFA</option>
                        <option value="€" <?= getConfig('devise') == '€' ? 'selected' : '' ?>>Euro (€)</option>
                        <option value="$" <?= getConfig('devise') == '$' ? 'selected' : '' ?>>Dollar ($)</option>
                    </select>
                </div>
            </div>
        </div>

        <!--  SECTION VENTES -->
        <div class="config-card">
            <div class="card-header">
                <i class="bx bx-receipt"></i>
                <h3>Paramètres ventes</h3>
            </div>
            
            <div class="form-group full-width">
                <label>Format du numéro de reçu</label>
                <input type="text" name="format_recu" 
                       value="<?= htmlspecialchars(getConfig('format_recu', 'ESP-{annee}-{numero}')) ?>"
                       placeholder="ESP-{annee}-{numero}">
                <small>
                    <i class="bx bx-info-circle"></i>
                    Variables disponibles: {annee}, {numero}
                    <br>
                    Exemple: ESP-2026-00001
                </small>
            </div>
        </div>

        <!--  BOUTONS SAUVEGARDE -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class="bx bx-save"></i>
                Sauvegarder la configuration
            </button>
            <a href="dashboard.php" class="btn-cancel">
                <i class="bx bx-x"></i>
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
//  Nettoyer l'alerte après 3 secondes
setTimeout(function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.transition = 'opacity 0.5s';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
    }
}, 3000);
</script>

<?php include 'pied.php'; ?>