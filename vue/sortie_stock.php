<?php
include 'entete.php';
require_once '../model/fonction.php';
requireGestionnaire(); //  ADMIN + GESTIONNAIRE

$id_article = $_GET['id'] ?? null;
$article = null;

if ($id_article) {
    $article = getArticleById($id_article);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = $_POST['id_article'];
    $quantite = $_POST['quantite'];
    $motif = $_POST['motif'] ?? 'Sortie de stock';
    
    $resultat = ajouterSortieStock($id_article, $quantite, $motif);
    
    if ($resultat) {
        $_SESSION['message'] = [
            'text' => " Sortie de stock effectuée avec succès",
            'type' => 'success'
        ];
       echo '<script>window.location.href = "stock.php";</script>';
exit;
    } else {
        $erreur = " Stock insuffisant ou erreur lors de la sortie";
    }
}
?>

<div class="home-content">
    <div class="form-container">
        <div class="form-header">
            <i class="bx bx-log-out-circle"></i>
            <h2>Sortie de stock</h2>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert danger"><?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST" class="stock-form">
            <?php if (!$id_article): ?>
                <!-- Sélection article -->
                <div class="form-group">
                    <label>Article</label>
                    <select name="id_article" required>
                        <option value="">Sélectionner un article</option>
                        <?php
                        $articles = getArticleActif();
                        foreach ($articles as $a):
                        ?>
                        <option value="<?= $a['id'] ?>" <?= ($a['quantite'] <= 0) ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($a['nom_article']) ?> 
                            (Stock: <?= $a['quantite'] ?>)
                            <?= ($a['quantite'] <= 0) ? ' - RUPTURE' : '' ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="id_article" value="<?= $article['id'] ?>">
                <div class="form-group">
                    <label>Article</label>
                    <input type="text" value="<?= htmlspecialchars($article['nom_article']) ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Stock disponible</label>
                    <input type="text" value="<?= $article['quantite'] ?>" readonly 
                           class="<?= $article['quantite'] <= 0 ? 'stock-rupture' : '' ?>">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="quantite">Quantité à retirer</label>
                <input type="number" name="quantite" id="quantite" min="1" required 
                       placeholder="Ex: 5"
                       <?= ($article && $article['quantite'] <= 0) ? 'disabled' : '' ?>>
            </div>

            <div class="form-group">
                <label for="motif">Motif (optionnel)</label>
                <input type="text" name="motif" id="motif" 
                       placeholder="Ex: Vente, utilisation interne...">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit" <?= ($article && $article['quantite'] <= 0) ? 'disabled' : '' ?>>
                    <i class="bx bx-check"></i>
                    Valider la sortie
                </button>
                <a href="stock.php" class="btn-cancel">
                    <i class="bx bx-x"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'pied.php'; ?>