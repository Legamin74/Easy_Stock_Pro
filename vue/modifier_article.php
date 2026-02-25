<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

if (empty($_GET['id'])) {
    header('Location: article.php');
    exit;
}

$article = getArticle($_GET['id'], true); // true pour inclure les archivés si besoin
if (!$article) {
    $_SESSION['message'] = [
        'text' => ' Article introuvable',
        'type' => 'danger'
    ];
    header('Location: article.php');
    exit;
}

$categories = getCategorie();
?>

<div class="home-content">
    <div class="form-section">
        <div class="form-card">
            <h3 class="form-title">
                <i class="bx bx-edit"></i> 
                Modifier l'article
            </h3>

            <form action="../model/modifierArticle.php" method="POST" class="article-form">
                <input type="hidden" name="id" value="<?= $article['id'] ?>">

                <div class="form-row">
                <?php if (!empty($article['image'])): ?>
                    <div class="form-group">
                        <label>Image actuelle</label>
                        <div>
                            <img src="../public/<?= $article['image'] ?>" alt="Image" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                        </div>
                    </div>
                 <?php endif; ?>

                    <!-- Upload nouvelle image -->
                    <div class="form-group">
                        <label> Changer l'image (optionnel)</label>
                        <input type="file" name="image" accept="image/*" class="form-control">
                        <input type="hidden" name="image_actuelle" value="<?= $article['image'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Nom de l'article</label>
                        <input type="text" name="nom_article" value="<?= htmlspecialchars($article['nom_article']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Catégorie</label>
                        <select name="id_categorie" required>
                            <option value="">-- Choisir --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" 
                                    <?= $cat['id'] == $article['id_categorie'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['libelle_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Quantité</label>
                        <input type="number" name="quantite" value="<?= $article['quantite'] ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Prix unitaire (FCFA)</label>
                        <input type="number" name="prix_unitaire" value="<?= $article['prix_unitaire'] ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" value="<?= $article['seuil_alerte'] ?? 5 ?>">
                    </div>

                    <div class="form-group">
                        <label>Date d'expiration</label>
                        <input type="date" name="date_expiration" 
                               value="<?= !empty($article['date_expiration']) && $article['date_expiration'] != '0000-00-00' ? $article['date_expiration'] : '' ?>">
                    </div>
                    
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        <i class="bx bx-save"></i> Mettre à jour
                    </button>
                    <a href="article.php" class="btn-cancel">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'pied.php'; ?>