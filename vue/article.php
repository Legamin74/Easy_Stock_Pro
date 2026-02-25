<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

// Récupération des articles
$articles = getArticle(null, false); // actifs uniquement
$categories = getCategorie();
?>
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert-message <?= $_SESSION['message']['type'] ?>">
        <i class="bx <?= $_SESSION['message']['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle' ?>"></i>
        <span><?= $_SESSION['message']['text'] ?></span>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="home-content">

    <div class="page-header">
        <h2> Gestion des articles</h2>
        <div class="header-tabs">
            <a href="article.php" class="tab-link active"> Actifs</a>
            <a href="article_archive.php" class="tab-link"> Archives</a>
        </div>
    </div>

  
    <div class="form-section">
        <div class="form-card">
            <h3 class="form-title">
                
                Ajouter un nouvel article
            </h3>

            <form action="../model/ajoutArticle.php" method="POST" class="article-form" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label>Nom de l'article</label>
                        <input type="text" name="nom_article" placeholder="Ex: PC Portable" required>
                    </div>

                    <div class="form-group">
                        <label> Catégorie</label>
                        <select name="id_categorie" required>
                            <option value="">-- Choisir une catégorie --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>">
                                    <?= htmlspecialchars($cat['libelle_categorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label> Quantité</label>
                        <input type="number" name="quantite" placeholder="Ex: 50" min="0" required>
                    </div>

                    <div class="form-group">
                        <label> Prix unitaire (FCFA)</label>
                        <input type="number" name="prix_unitaire" placeholder="Ex: 15000" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label> Date d'enregistrement</label>
                        <input type="datetime-local" name="date_creation" value="<?= date('Y-m-d\TH:i') ?>">
                    </div>

                    <div class="form-group">
                        <label> Date d'expiration (optionnelle)</label>
                        <input type="date" name="date_expiration">
                    </div>
                    <div class="form-group">
                        <label>Seuil d'alerte</label>
                        <input type="number" name="seuil_alerte" value="5" min="1" class="form-control">
                        <small>Stock minimum avant alerte</small>
                    </div>
                </div>
                 <div class="form-group">
                    <label> Image de l'article (optionnelle)</label>
                    <input type="file" name="image" accept="image/*" class="form-control">
                    <small>Formats : JPG, PNG, GIF (Max 2 Mo)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">
                        </i> Enregistrer l'article
                    </button>
                </div>
            </form>
        </div>
    </div>

  
    <div class="list-section">
        <h3 class="list-title">
            <i class="bx bx-list-ul"></i>
            Liste des articles actifs
        </h3>

        <div class="table-responsive">
            <table class="article-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Date d'ajout</th>
                        <th>Expiration</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($articles)): ?>
                        <tr>
                            <td colspan="7" class="empty-message">
                                <i class="bx bx-package"></i>
                                <p>Aucun article trouvé</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        
                        <?php foreach ($articles as $a): ?>
                            
                        <tr>
                            <td>
                                 <?php if (!empty($a['image'])): ?>
                                    <a href="../public/<?= $a['image'] ?>" data-lightbox="article-<?= $a['id'] ?>" data-title="<?= htmlspecialchars($a['nom_article']) ?>">
                                         <img src="../public/<?= $a['image'] ?>" alt="Image" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px; cursor: pointer;">
                                     </a>
                                    <?php else: ?>
                                        <span style="color: #ccc;">Aucun image</span>
                                     <?php endif; ?>
                             </td>
                                    <td class="article-name">
                                <span class="product-icon"></span>
                                <?= htmlspecialchars($a['nom_article']) ?>
                            </td>
                            <td><?= htmlspecialchars($a['categorie'] ?? 'Non catégorisé') ?></td>
                            <td class="text-center"><?= $a['quantite'] ?></td>
                            <td class="text-right"><?= number_format($a['prix_unitaire'], 0, ',', ' ') ?> F</td>
                            <td><?= date('d/m/Y', strtotime($a['date_creation'] ?? 'now')) ?></td>
                            <td>
                                <?= !empty($a['date_expiration']) && $a['date_expiration'] != '0000-00-00' 
                                    ? date('d/m/Y', strtotime($a['date_expiration'])) 
                                    : '—' ?>
                            </td>
                            <td class="actions">
                                <a href="detailsArticle.php?id=<?= $a['id'] ?>" 
                                   class="btn-icon details" 
                                   title="Détails">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="modifier_article.php?id=<?= $a['id'] ?>" 
                                   class="btn-icon edit" 
                                   title="Modifier">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="../model/archiverArticle.php?id=<?= $a['id'] ?>" 
                                   class="btn-icon archive" 
                                   onclick="return confirm('Archiver cet article ?')"
                                   title="Archiver">
                                    <i class="bx bx-archive"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'pied.php'; ?>