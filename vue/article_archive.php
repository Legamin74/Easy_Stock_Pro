<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

// Récupération des articles archivés
$articles = getArticleArchive();
?>

<div class="home-content">
    <!-- ======================================== -->
    <!--         EN-TÊTE AVEC ONGLETS              -->
    <!-- ======================================== -->
    <div class="page-header">
        <h2><i class="bx bx-archive"></i> Gestion des articles</h2>
        <div class="header-tabs">
            <a href="article.php" class="tab-link"> Actifs</a>
            <a href="article_archive.php" class="tab-link active"> Archives</a>
        </div>
    </div>

    <!-- ======================================== -->
    <!--      SECTION LISTE DES ARTICLES          -->
    <!-- ======================================== -->
    <div class="list-section">
        <h3 class="list-title">
            <i class="bx bx-archive"></i>
            Articles archivés
        </h3>

        <div class="table-responsive">
            <table class="article-table">
                <thead>
                    <tr>
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
                                <i class="bx bx-archive"></i>
                                <p>Aucun article archivé</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($articles as $a): ?>
                        <tr>
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
                                <a href="details_article.php?id=<?= $a['id'] ?>" 
                                   class="btn-icon details" 
                                   title="Détails">
                                    <i class="bx bx-show"></i>
                                </a>
                                <a href="../model/restaurerArticle.php?id=<?= $a['id'] ?>" 
                                   class="btn-icon restore" 
                                   onclick="return confirm('Restaurer cet article ?')"
                                   title="Restaurer">
                                    <i class="bx bx-refresh"></i>
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