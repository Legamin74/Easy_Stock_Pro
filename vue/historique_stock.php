<?php
include 'entete.php';
require_once '../model/fonction.php';

// Récupération des filtres
$id_article = $_GET['id_article'] ?? '';
$type = $_GET['type'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

// Construire les filtres
$filtres = [];
if ($id_article) $filtres['id_article'] = $id_article;
if ($type) $filtres['type'] = $type;
if ($date_debut) $filtres['date_debut'] = $date_debut;
if ($date_fin) $filtres['date_fin'] = $date_fin;

// Récupérer les mouvements
$mouvements = getMouvementsStockFiltres($filtres);

$articles = getArticle();
?>

<div class="home-content">
    <!-- EN-TÊTE -->
    <div class="historique-header">
        <div class="header-title">
            <i class="bx bx-history"></i>
            <h2>Historique des mouvements</h2>
        </div>
        <a href="stock.php" class="btn-back">
            <i class="bx bx-arrow-back"></i>
            Retour au stock
        </a>
    </div>

    <!-- FILTRES -->
    <div class="filtres-box">
        <form method="GET" class="filtres-form">
            <div class="filtres-grid">
                <div class="filtre-groupe">
                    <label>Article</label>
                    <select name="id_article">
                        <option value="">Tous les articles</option>
                        <?php foreach ($articles as $a): ?>
                        <option value="<?= $a['id'] ?>" <?= $id_article == $a['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['nom_article']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filtre-groupe">
                    <label>Type</label>
                    <select name="type">
                        <option value="">Tous les types</option>
                        <option value="entree" <?= $type == 'entree' ? 'selected' : '' ?>>➕ Entrée</option>
                        <option value="sortie" <?= $type == 'sortie' ? 'selected' : '' ?>>➖ Sortie</option>
                        <option value="ajustement" <?= $type == 'ajustement' ? 'selected' : '' ?>>✏️ Ajustement</option>
                    </select>
                </div>

                <div class="filtre-groupe">
                    <label>Date début</label>
                    <input type="date" name="date_debut" value="<?= $date_debut ?>">
                </div>

                <div class="filtre-groupe">
                    <label>Date fin</label>
                    <input type="date" name="date_fin" value="<?= $date_fin ?>">
                </div>
            </div>

            <div class="filtres-actions">
                <button type="submit" class="btn-filtrer">
                    <i class="bx bx-filter-alt"></i>
                    Filtrer
                </button>
                <a href="historique_stock.php" class="btn-reset">
                    <i class="bx bx-reset"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- STATISTIQUES RAPIDES -->
    <?php if (!empty($mouvements)): 
        $total_entrees = 0;
        $total_sorties = 0;
        foreach ($mouvements as $m) {
            if ($m['type'] == 'entree') $total_entrees += $m['quantite'];
            if ($m['type'] == 'sortie') $total_sorties += $m['quantite'];
        }
    ?>
    <div class="stats-mini-cards">
        <div class="stat-mini-card">
            <div class="stat-mini-icon entre">
                <i class="bx bx-log-in-circle"></i>
            </div>
            <div class="stat-mini-info">
                <span class="stat-mini-label">Total entrées</span>
                <span class="stat-mini-value"><?= $total_entrees ?></span>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-icon sortie">
                <i class="bx bx-log-out-circle"></i>
            </div>
            <div class="stat-mini-info">
                <span class="stat-mini-label">Total sorties</span>
                <span class="stat-mini-value"><?= $total_sorties ?></span>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-mini-icon mouvement">
                <i class="bx bx-move"></i>
            </div>
            <div class="stat-mini-info">
                <span class="stat-mini-label">Total mouvements</span>
                <span class="stat-mini-value"><?= count($mouvements) ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TABLEAU DES MOUVEMENTS -->
    <div class="historique-table-container">
        <?php if (!empty($mouvements)): ?>
        <table class="historique-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Article</th>
                    <th>Type</th>
                    <th>Quantité</th>
                    <th>Stock avant</th>
                    <th>Stock après</th>
                    <th>Motif</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mouvements as $m): ?>
                    <?php
                        $type_class = '';
                        $type_icon = '';
                        $type_label = '';
                        
                        if ($m['type'] == 'entree') {
                            $type_class = 'type-entree';
                            $type_icon = 'bx-log-in-circle';
                            $type_label = 'Entrée';
                        } elseif ($m['type'] == 'sortie') {
                            $type_class = 'type-sortie';
                            $type_icon = 'bx-log-out-circle';
                            $type_label = 'Sortie';
                        } else {
                            $type_class = 'type-ajustement';
                            $type_icon = 'bx-adjust';
                            $type_label = 'Ajustement';
                        }
                    ?>
                    <tr>
                        <td class="date-cell">
                            <?= date('d/m/Y H:i', strtotime($m['date_mouvement'])) ?>
                        </td>
                        <td class="article-cell">
                            <span class="article-name"><?= htmlspecialchars($m['nom_article']) ?></span>
                        </td>
                        <td>
                            <span class="type-badge <?= $type_class ?>">
                                <i class="bx <?= $type_icon ?>"></i>
                                <?= $type_label ?>
                            </span>
                        </td>
                        <td class="quantite-cell <?= $type_class ?>">
                            <strong>
                                <?= $m['type'] == 'entree' ? '+' : '-' ?>
                                <?= $m['quantite'] ?>
                            </strong>
                        </td>
                        <td class="stock-cell"><?= $m['stock_avant'] ?></td>
                        <td class="stock-cell"><?= $m['stock_apres'] ?></td>
                        <td class="motif-cell">
                            <?= htmlspecialchars($m['motif'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="bx bx-history"></i>
            <h3>Aucun mouvement trouvé</h3>
            <p>Essayez de modifier vos filtres ou d'effectuer des entrées/sorties de stock.</p>
            <a href="stock.php" class="btn-primary">
                <i class="bx bx-package"></i>
                Gérer le stock
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Confirmation avant réinitialisation des filtres
document.querySelector('.btn-reset')?.addEventListener('click', function(e) {
    if (!confirm('Réinitialiser tous les filtres ?')) {
        e.preventDefault();
    }
});
</script>

<?php include 'pied.php'; ?>