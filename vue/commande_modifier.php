<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

if (empty($_GET['id'])) {
    header('Location: commande.php');
    exit;
}

$id_commande = (int)$_GET['id'];
$commande = getCommandeById($id_commande);

if (!$commande) {
    $_SESSION['message'] = ['text' => 'Commande introuvable', 'type' => 'danger'];
    header('Location: commande.php');
    exit;
}

if ($commande['statut'] != 'en_attente') {
    $_SESSION['message'] = ['text' => 'Cette commande ne peut pas être modifiée', 'type' => 'danger'];
    header('Location: commande_details.php?id=' . $id_commande);
    exit;
}

$fournisseurs = getFournisseurActif();
?>

<style>
.modif-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.modif-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #5f6b7a;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    border-color: #0b5e2e;
    outline: none;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-submit {
    background: #0b5e2e;
    color: white;
    border: none;
    padding: 14px 30px;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
}

.btn-submit:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.btn-cancel {
    background: #f5f5f5;
    color: #666;
    border: 1px solid #ddd;
    padding: 14px 30px;
    border-radius: 10px;
    text-decoration: none;
    font-size: 16px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-cancel:hover {
    background: #e8e8e8;
}

.reference {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.reference strong {
    color: #0b5e2e;
    font-size: 18px;
}
</style>

<div class="home-content">
    <div class="modif-container">
        <div class="modif-card">
            <h2 style="margin-bottom: 25px;">
                <i class="bx bx-edit"></i> Modifier la commande
            </h2>
            
            <div class="reference">
                <i class="bx bx-package" style="font-size: 24px; color: #0b5e2e;"></i>
                <span>
                    Commande <strong><?= $commande['reference'] ?? 'CMD-'.$commande['id'] ?></strong>
                </span>
            </div>
            
            <form action="../model/modifierCommande.php" method="POST">
                <input type="hidden" name="id_commande" value="<?= $commande['id'] ?>">
                
                <div class="form-group">
                    <label>Fournisseur</label>
                    <select name="id_fournisseur" required>
                        <option value="">-- Choisir un fournisseur --</option>
                        <?php foreach ($fournisseurs as $f): ?>
                            <option value="<?= $f['id'] ?>" 
                                <?= $f['id'] == $commande['id_fournisseur'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($f['prenom'] . ' ' . $f['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Date livraison prévue</label>
                        <input type="date" name="date_livraison" 
                               value="<?= $commande['date_livraison_prevue'] ?? '' ?>"
                               min="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Statut actuel</label>
                        <input type="text" value="<?= ucfirst($commande['statut']) ?>" readonly style="background:#f5f5f5;">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" rows="4"><?= htmlspecialchars($commande['notes'] ?? '') ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bx bx-save"></i> Enregistrer
                    </button>
                    <a href="commande_details.php?id=<?= $commande['id'] ?>" class="btn-cancel">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'pied.php'; ?>