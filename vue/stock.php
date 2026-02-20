<?php
include 'entete.php';
if (isset($_SESSION['message'])): ?>
    <div class="alert <?= $_SESSION['message']['type'] ?>">
        <?= $_SESSION['message']['text'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; 
require_once '../model/fonction.php';
requireGestionnaire(); //  ADMIN + GESTIONNAIRE

//  Récupération des VRAIES données
$alert=getconfig('seuil_alerte_global','10');
$devise = getConfig('devise', 'FCFA');
$total_articles = getTotalArticles();
$stock_faible = getStockFaible();
$rupture_stock = getRuptureStock();
$valeur_stock = getValeurStock();
$alertes = getAlertesStock();
$articles = getArticlesStock();
?>

<div class="home-content">
    <!-- EN-TÊTE DU MODULE STOCK -->
    <div class="stock-header">
        <div class="stock-title">
            <i class="bx bx-box"></i>
            <span>Gestion de stock</span>
        </div>
        <a href="article.php" class="btn-add">
            <i class="bx bx-plus"></i>
            Nouvel article
        </a>
    </div>

    <!--  CARTES STATISTIQUES -->
    <div class="stock-cards">
        <div class="card">
            <div class="card-icon">
                <i class="bx bx-package"></i>
            </div>
            <div class="card-info">
                <span class="card-label">Total articles</span>
                <span class="card-value"><?= $total_articles ?></span>
            </div>
        </div>
        <div class="card warning">
            <div class="card-icon">
                <i class="bx bx-error"></i>
            </div>
            <div class="card-info">
                <span class="card-label">Stock faible</span>
                <span class="card-value"><?= $stock_faible ?></span>
            </div>
        </div>
        <div class="card danger">
            <div class="card-icon">
                <i class="bx bx-error-circle"></i>
            </div>
            <div class="card-info">
                <span class="card-label">Rupture</span>
                <span class="card-value"><?= $rupture_stock ?></span>
            </div>
        </div>
        <div class="card success">
            <div class="card-icon">
                <i class="bx bx-coin-stack"></i>
            </div>
            <div class="card-info">
                <span class="card-label">Valeur stock</span>
                <span class="card-value"><?= number_format($valeur_stock, 0, ',', ' ') ?> <?= $devise ?></span>
            </div>
        </div>
    </div>

    <!--  ALERTES STOCK -->
    <?php if (!empty($alertes)): ?>
    <div class="stock-alertes">
        <div class="section-header">
            <i class="bx bx-bell"></i>
            <h3>Alertes stock (<?= count($alertes) ?>)</h3>
        </div>
        <div class="alertes-list">
            <?php foreach ($alertes as $alerte): ?>
                <div class="alerte-item <?= $alerte['statut'] ?>">
                    <div class="alerte-icon">
                        <i class="bx bx-<?= $alerte['statut'] == 'rupture' ? 'x-circle' : 'error-circle' ?>"></i>
                    </div>
                    <div class="alerte-content">
                        <strong><?= htmlspecialchars($alerte['nom_article']) ?></strong>
                        <?php if ($alerte['statut'] == 'rupture'): ?>
                            <span class="badge danger">Rupture de stock</span>
                            <span class="alerte-action">Commander d'urgence</span>
                        <?php else: ?>
                            <span class="badge warning">Stock faible</span>
                            <span class="alerte-action"><?= $alerte['quantite'] ?>/<?= $alerte['seuil_alerte'] ?> restants</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!--  LISTE DES ARTICLES AVEC ÉTAT -->
    <div class="stock-liste">
        <div class="section-header">
            <i class="bx bx-list-ul"></i>
            <h3>Liste des articles</h3>
            <div class="header-actions">
                <input type="text" id="searchStock" placeholder="Rechercher un article..." class="search-stock">
                <button class="btn-filter">
                    <i class="bx bx-filter"></i>
                    Filtre
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="stock-table" id="stockTable">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Catégorie</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Seuil</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($articles)): ?>
                        <?php foreach ($articles as $article): ?>
                            <?php 
                                $statut_class = '';
                                $statut_label = '';
                                $statut_icon = '';
                                
                                if ($article['quantite'] <= 0) {
                                    $statut_class = 'statut-rupture';
                                    $statut_label = 'Rupture';
                                    $statut_icon = 'bx-x-circle';
                                } elseif ($article['quantite'] <= $article['seuil_alerte']) {
                                    $statut_class = 'statut-faible';
                                    $statut_label = 'Stock faible';
                                    $statut_icon = 'bx-error-circle';
                                } else {
                                    $statut_class = 'statut-ok';
                                    $statut_label = 'OK';
                                    $statut_icon = 'bx-check-circle';
                                }
                            ?>
                            <tr>
                                <td class="article-name">
                                    
                                    <?= htmlspecialchars($article['nom_article']) ?>
                                </td>
                                <td><?= htmlspecialchars($article['categorie']) ?></td>
                                <td class="price"><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> <?= $devise ?></td>
                                <td class="quantity <?= $statut_class ?>">
                                    <strong><?= $article['quantite'] ?></strong>
                                </td>
                                <td><?= $article['seuil_alerte'] ?></td>
                                <td>
                                    <span class="statut-badge <?= $statut_class ?>">
                                        <i class="bx <?= $statut_icon ?>"></i>
                                        <?= $statut_label ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    
                                   <a href="modifier_article.php?id=<?= $article['id'] ?>" class="action-btn edit" title="Modifier">
                                        <i class="bx bx-edit"></i>
                                    </a>
                                    <a href="entree_stock.php?id=<?= $article['id'] ?>" class="action-btn entre" title="Entrée de stock">
                                        <i class="bx bx-plus-circle"></i>
                                    </a>
                                    <a href="sortie_stock.php?id=<?= $article['id'] ?>" class="action-btn sortie" title="Sortie de stock">
                                        <i class="bx bx-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px;">
                                <i class="bx bx-package" style="font-size: 48px; color: #ccc;"></i>
                                <p style="margin-top: 10px; color: #666;">Aucun article en stock</p>
                                <a href="article.php" style="color: rgb(6, 88, 6); margin-top: 10px; display: inline-block;">
                                    + Ajouter un article
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!--  BOUTONS D'ACTIONS RAPIDES -->
   <!-- <div class="stock-actions">
        <a href="entree_stock.php" class="action-card">
            <i class="bx bx-log-in-circle"></i>
            <span>Entrée de stock</span>
        </a>
        <a href="sortie_stock.php" class="action-card">
            <i class="bx bx-log-out-circle"></i>
            <span>Sortie de stock</span>
        </a>
        <a href="inventaire.php" class="action-card">
            <i class="bx bx-adjust"></i>
            <span>Ajustement</span>
        </a>
        <a href="export_stock.php" class="action-card">
            <i class="bx bx-file-export"></i>
            <span>Exporter</span>
        </a>
    </div>-->
</div>

<script>
// Recherche dynamique dans le tableau
document.getElementById('searchStock').addEventListener('keyup', function() {
    let search = this.value.toLowerCase();
    let rows = document.querySelectorAll('#stockTable tbody tr');
    
    rows.forEach(row => {
        let text = row.textContent.toLowerCase();
        row.style.display = text.includes(search) ? '' : 'none';
    });
});
</script>

<?php include 'pied.php'; ?>
<style>
    /* ================= MODULE STOCK ================= */

.stock-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.stock-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    font-weight: 600;
    color: rgb(1, 62, 1);
}

.stock-title i {
    font-size: 28px;
}

.btn-add {
    background: rgb(6, 88, 6);
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-add:hover {
    background: rgb(1, 62, 1);
    transform: translateY(-2px);
}

/* Cartes statistiques */
.stock-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stock-cards .card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: 0.3s;
}

.stock-cards .card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.card-icon {
    width: 60px;
    height: 60px;
    background: rgba(6, 88, 6, 0.1);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card-icon i {
    font-size: 32px;
    color: rgb(6, 88, 6);
}

.card.warning .card-icon {
    background: rgba(255, 167, 38, 0.1);
}

.card.warning .card-icon i {
    color: #ffa726;
}

.card.danger .card-icon {
    background: rgba(244, 67, 54, 0.1);
}

.card.danger .card-icon i {
    color: #f44336;
}

.card.success .card-icon {
    background: rgba(43, 212, 125, 0.1);
}

.card.success .card-icon i {
    color: #2bd47d;
}

.card-info {
    display: flex;
    flex-direction: column;
}

.card-label {
    font-size: 14px;
    color: #666;
}

.card-value {
    font-size: 28px;
    font-weight: 600;
    color: #333;
}

/* Alertes stock */
.stock-alertes {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.section-header i {
    font-size: 24px;
    color: rgb(6, 88, 6);
}

.section-header h3 {
    color: #333;
    font-size: 18px;
    font-weight: 600;
}

.alertes-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.alerte-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid transparent;
}

.alerte-item.rupture {
    border-left-color: #f44336;
    background: rgba(244, 67, 54, 0.05);
}

.alerte-item.faible {
    border-left-color: #ffa726;
    background: rgba(255, 167, 38, 0.05);
}

.alerte-icon i {
    font-size: 24px;
}

.alerte-item.rupture .alerte-icon i {
    color: #f44336;
}

.alerte-item.faible .alerte-icon i {
    color: #ffa726;
}

.alerte-content {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.badge.danger {
    background: #f44336;
    color: white;
}

.badge.warning {
    background: #ffa726;
    color: white;
}

.alerte-action {
    color: rgb(6, 88, 6);
    font-weight: 500;
    font-size: 13px;
}

/* Liste des articles */
.stock-liste {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.header-actions {
    display: flex;
    gap: 10px;
    margin-left: auto;
}

.search-stock {
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    width: 250px;
    font-size: 14px;
}

.search-stock:focus {
    outline: none;
    border-color: rgb(6, 88, 6);
}

.btn-filter {
    padding: 10px 15px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: 0.3s;
}

.btn-filter:hover {
    background: #e9ecef;
}

.table-responsive {
    overflow-x: auto;
}

.stock-table {
    width: 100%;
    border-collapse: collapse;
}

.stock-table th {
    text-align: left;
    padding: 15px 10px;
    color: #666;
    font-weight: 600;
    font-size: 14px;
    border-bottom: 2px solid #eee;
}

.stock-table td {
    padding: 15px 10px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.stock-table tbody tr:hover {
    background: #f8f9fa;
}

.article-name {
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.product-icon {
    font-size: 20px;
}

.quantity {
    font-weight: 600;
}

.quantity.statut-rupture {
    color: #f44336;
}

.quantity.statut-faible {
    color: #ffa726;
}

.quantity.statut-ok {
    color: #2bd47d;
}

.statut-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.statut-badge.statut-rupture {
    background: rgba(244, 67, 54, 0.1);
    color: #f44336;
}

.statut-badge.statut-faible {
    background: rgba(255, 167, 38, 0.1);
    color: #ffa726;
}

.statut-badge.statut-ok {
    background: rgba(43, 212, 125, 0.1);
    color: #2bd47d;
}

.actions {
    display: flex;
    gap: 10px;
}

.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.action-btn.entre {
    background: #2bd47d;
}

.action-btn.sortie {
    background: #ffa726;
}

.action-btn.edit {
    background: #2196f3;
}

.action-btn:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}

/* Actions rapides */
.stock-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.action-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #333;
    transition: 0.3s;
}

.action-card:hover {
    transform: translateY(-5px);
    background: rgb(6, 88, 6);
    color: white;
}

.action-card i {
    font-size: 32px;
    color: rgb(6, 88, 6);
}

.action-card:hover i {
    color: white;
}

.action-card span {
    font-size: 14px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 700px) {
    .stock-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .search-stock {
        width: 100%;
    }
    
    .stock-table td, 
    .stock-table th {
        padding: 10px;
    }
    
    .alerte-content {
        flex-direction: column;
        align-items: flex-start;
    }
}

@media (max-width: 700px) {
    .stock-cards {
        grid-template-columns: 1fr;
    }
    
    .actions {
        flex-direction: column;
    }
}
</style>

<?php include 'pied.php'; ?>