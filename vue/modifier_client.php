<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

if (empty($_GET['id'])) {
    header('Location: client.php');
    exit;
}

$client = getClient($_GET['id'], true); // inclure archivés si besoin
if (!$client) {
    $_SESSION['message'] = ['text' => 'Client introuvable', 'type' => 'danger'];
    header('Location: client.php');
    exit;
}
?>

<div class="home-content">
    <div class="form-section">
        <div class="form-card">
            <h3 class="form-title">
                <i class="bx bx-edit"></i> 
                Modifier le client
            </h3>

            <form action="../model/modifierClient.php" method="POST" class="client-form">
                <input type="hidden" name="id" value="<?= $client['id'] ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($client['nom']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($client['prenom']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($client['telephone']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Adresse</label>
                        <input type="text" name="adresse" value="<?= htmlspecialchars($client['adresse'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bx bx-save"></i> Mettre à jour
                    </button>
                    <a href="client.php" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'pied.php'; ?>