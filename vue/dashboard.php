<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();
$devise = getConfig('devise', 'FCFA');
$total_articles = getTotalArticles();
$stock_faible = getStockFaible();
$rupture_stock = getRuptureStock();
$valeur_stock = getValeurStock();
$alertes = getAlertesStock();
$articles_stock = getArticlesStock();
$ventes = getLastVentes(10);
$top_articles = getTopVentes(10);
$ca = getCA();
$all_ventes = getAllVente();
$all_commandes = getAllCommande();
$all_articles = getAllArticle();
?>

<style>
/* ================= STYLE DIRECT DASHBOARD ================= */

/* Reset des marges et padding */
* {
    box-sizing: border-box;
}

/* Cartes statistiques */
.overview-boxes {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.box {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.right-side {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.box-topic {
    font-size: 14px;
    color: #666;
}

.number {
    font-size: 28px;
    font-weight: 600;
    color: rgb(1, 62, 1);
}

.indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
}

.cart {
    font-size: 40px;
    color: rgb(6, 88, 6);
    padding: 10px;
    border-radius: 10px;
    background: rgba(6, 88, 6, 0.1);
}

/* ================= TABLEAU VENTES ================= */
.sales-boxes {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-top: 25px;
}

.recent-sales, .top-sales {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.title {
    font-size: 18px;
    font-weight: 600;
    color: rgb(1, 62, 1);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f0f0f0;
}

/* Style du tableau */
.dashboard-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.dashboard-table th {
    background: rgb(6, 88, 6);
    color: white;
    padding: 12px 15px;
    text-align: left;
    font-weight: 500;
    white-space: nowrap;
}

.dashboard-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    vertical-align: top;
}

.dashboard-table tbody tr:hover {
    background: #f8f9fa;
}

.date-cell {
    white-space: nowrap;
    font-weight: 500;
}

.date-cell small {
    color: #999;
    font-size: 11px;
    display: block;
}

.articles-cell {
    min-width: 150px;
}

.article-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 3px 0;
    border-bottom: 1px dashed #eee;
}

.article-line:last-child {
    border-bottom: none;
}

.article-name {
    color: #333;
}

.article-qty {
    color: rgb(6, 88, 6);
    font-weight: bold;
    margin-left: 10px;
}

.prix-cell {
    text-align: right;
    white-space: nowrap;
}

.prix-line {
    padding: 3px 0;
    border-bottom: 1px dashed #eee;
}

.prix-line:last-child {
    border-bottom: none;
}

.total-cell {
    text-align: right;
    font-weight: bold;
    color: rgb(6, 88, 6);
    font-size: 16px;
    white-space: nowrap;
}

/* ================= TABLEAU TOP VENTES ================= */
.top-table {
    width: 100%;
    border-collapse: collapse;
}

.top-table th {
    background: #f0f0f0;
    color: #333;
    padding: 12px 15px;
    text-align: left;
    font-weight: 600;
}

.top-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
}

.top-table tr:last-child td {
    border-bottom: none;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

/* Bouton Voir Tout */
.button {
    margin-top: 20px;
    text-align: right;
}

.btn-view {
    background: rgb(6, 88, 6);
    color: white;
    padding: 8px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    display: inline-block;
}

.btn-view:hover {
    background: rgb(1, 62, 1);
}

/* Responsive */
@media (max-width: 992px) {
    .sales-boxes {
        grid-template-columns: 1fr;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .dashboard-table {
        min-width: 900px;
    }
}
</style>

<div class="home-content">
    <!-- ================= CARTES STATISTIQUES ================= -->
    <div class="overview-boxes">
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Commandes</div>
                <div class="number"><?= $all_commandes['nbre'] ?? 0 ?></div>
            </div>
            <i class="bx bx-cart-alt cart"></i>
        </div>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Ventes</div>
                <div class="number"><?= count(getAllVente()) ?></div>
            </div>
            <i class="bx bxs-cart-add cart two"></i>
        </div>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Articles</div>
                <div class="number"><?= $all_articles['nbre'] ?? 0 ?></div>
            </div>
            <i class="bx bx-cart cart three"></i>
        </div>
        <?php if (estAdmin()): ?>
        <div class="box">
            <div class="right-side">
                <div class="box-topic">Chiffre d'affaire</div>
                <div class="number"><?= number_format($ca, 0, ',', ' ') ?> <?= $devise ?></div>
            </div>
            <i class="bx bxs-cart-download cart four"></i>
        </div>
        <?php endif; ?>
    </div>

   
<!-- ======================================== -->
<!--         SECTION GRAPHIQUES                -->
<!-- ======================================== -->
<div class="charts-row">
    <!-- Graphique 1 : Évolution des ventes -->
    <div class="chart-card">
        <h3 class="chart-title">
            <i class="bx bx-line-chart"></i>
            Évolution des ventes (7 derniers jours)
        </h3>
        <canvas id="chartVentes" height="200"></canvas>
    </div>

    <!-- Graphique 2 : Répartition par catégorie -->
    <div class="chart-card">
        <h3 class="chart-title">
            <i class="bx bx-pie-chart-alt"></i>
            Ventes par catégorie
        </h3>
        <canvas id="chartCategories" height="200"></canvas>
    </div>
</div>

<div class="charts-row">
    <!-- Graphique 3 : Top 5 articles -->
    <div class="chart-card">
        <h3 class="chart-title">
            <i class="bx bx-bar-chart-alt"></i>
            Top 5 articles les plus vendus
        </h3>
        <canvas id="chartTopArticles" height="200"></canvas>
    </div>

    <!-- Graphique 4 : Alertes stock (optionnel) -->
    <div class="chart-card">
        <h3 class="chart-title">
            <i class="bx bx-error"></i>
            État du stock
        </h3>
        <canvas id="chartStock" height="200"></canvas>
    </div>
</div>
<script>
// 1. Graphique des ventes sur 7 jours
<?php
$ventes7j = getVentes7Jours();
$jours = [];
$ca = [];
foreach ($ventes7j as $v) {
    $jours[] = date('d/m', strtotime($v['jour']));
    $ca[] = $v['ca'] ?? 0;
}
?>
const ctx1 = document.getElementById('chartVentes').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode($jours) ?>,
        datasets: [{
            label: 'Chiffre d\'affaires (FCFA)',
            data: <?= json_encode($ca) ?>,
            borderColor: '#0b5e2e',
            backgroundColor: 'rgba(11, 94, 46, 0.1)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    }
});

// 2. Graphique des ventes par catégorie
<?php
$catData = getVentesParCategorie();
$catLabels = [];
$catValues = [];
foreach ($catData as $c) {
    $catLabels[] = $c['libelle_categorie'];
    $catValues[] = $c['ca'];
}
?>
const ctx2 = document.getElementById('chartCategories').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($catLabels) ?>,
        datasets: [{
            data: <?= json_encode($catValues) ?>,
            backgroundColor: ['#0b5e2e', '#2196f3', '#ff9800', '#9c27b0', '#f44336']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// 3. Top 5 articles
<?php
$top5 = getTop5Articles();
$topLabels = [];
$topQt = [];
foreach ($top5 as $t) {
    $topLabels[] = $t['nom_article'];
    $topQt[] = $t['total_qte'];
}
?>
const ctx3 = document.getElementById('chartTopArticles').getContext('2d');
new Chart(ctx3, {
    type: 'bar',
    data: {
        labels: <?= json_encode($topLabels) ?>,
        datasets: [{
            label: 'Quantité vendue',
            data: <?= json_encode($topQt) ?>,
            backgroundColor: '#0b5e2e'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        }
    }
});

// 4. État du stock
<?php
$stockData = [
    'Stock normal' => getTotalArticles() - getStockFaible() - getRuptureStock(),
    'Stock faible' => getStockFaible(),
    'Rupture' => getRuptureStock()
];
?>
const ctx4 = document.getElementById('chartStock').getContext('2d');
new Chart(ctx4, {
    type: 'doughnut',
    data: {
        labels: ['Stock normal', 'Stock faible', 'Rupture'],
        datasets: [{
            data: [<?= $stockData['Stock normal'] ?>, <?= $stockData['Stock faible'] ?>, <?= $stockData['Rupture'] ?>],
            backgroundColor: ['#4caf50', '#ff9800', '#f44336']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

<?php include 'pied.php'; ?>