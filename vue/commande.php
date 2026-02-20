<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

$commandes = getAllCommandes();
$stats = countCommandesByStatut();

// Organiser les stats par statut
$stats_by_status = [];
foreach ($stats as $s) {
    $stats_by_status[$s['statut']] = $s;
}
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    color: #1a2b3c;
    margin: 0;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    border: 1px solid #f0f0f0;
}

.stat-card .stat-title {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.stat-card .stat-number {
    font-size: 32px;
    font-weight: 600;
    color: #1a2b3c;
}

.stat-card .stat-total {
    font-size: 14px;
    color: #0b5e2e;
    margin-top: 5px;
}

.stat-card.en-attente { border-top: 4px solid #ff9800; }
.stat-card.validee { border-top: 4px solid #2196f3; }
.stat-card.livree { border-top: 4px solid #4caf50; }
.stat-card.annulee { border-top: 4px solid #f44336; }

.btn-add {
    background: #0b5e2e;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}

.btn-add:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    padding: 5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.commande-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.commande-table th {
    background: #0b5e2e;
    color: white;
    padding: 15px 12px;
    text-align: left;
}

.commande-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.commande-table tbody tr:hover {
    background: #f8f9fa;
}

.badge-statut {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge-en-attente {
    background: #fff3e0;
    color: #e65100;
}

.badge-validee {
    background: #e3f2fd;
    color: #1565c0;
}

.badge-livree {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-annulee {
    background: #ffebee;
    color: #c62828;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #0b5e2e;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.btn-action:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.text-right {
    text-align: right;
}
</style>

<div class="home-content">
    <!-- En-tete -->
    <div class="page-header">
        <h1>
            <i class="bx bx-list-ul"></i>
            Gestion des commandes
        </h1>
        <a href="commande_ajout.php" class="btn-add">
            <i class="bx bx-plus"></i> Nouvelle commande
        </a>
    </div>

    <!-- Statistiques -->
    <div class="stats-cards">
        <div class="stat-card en-attente">
            <div class="stat-title">
                <i class="bx bx-time"></i> En attente
            </div>
            <div class="stat-number"><?= $stats_by_status['en_attente']['nombre'] ?? 0 ?></div>
            <div class="stat-total">
                <?= number_format($stats_by_status['en_attente']['total'] ?? 0, 0, ',', ' ') ?> F
            </div>
        </div>
        <div class="stat-card validee">
            <div class="stat-title">
                <i class="bx bx-check-circle"></i> Validees
            </div>
            <div class="stat-number"><?= $stats_by_status['validee']['nombre'] ?? 0 ?></div>
            <div class="stat-total">
                <?= number_format($stats_by_status['validee']['total'] ?? 0, 0, ',', ' ') ?> F
            </div>
        </div>
        <div class="stat-card livree">
            <div class="stat-title">
                <i class="bx bx-check-double"></i> Livrees
            </div>
            <div class="stat-number"><?= $stats_by_status['livree']['nombre'] ?? 0 ?></div>
            <div class="stat-total">
                <?= number_format($stats_by_status['livree']['total'] ?? 0, 0, ',', ' ') ?> F
            </div>
        </div>
        <div class="stat-card annulee">
            <div class="stat-title">
                <i class="bx bx-x-circle"></i> Annulees
            </div>
            <div class="stat-number"><?= $stats_by_status['annulee']['nombre'] ?? 0 ?></div>
            <div class="stat-total">
                <?= number_format($stats_by_status['annulee']['total'] ?? 0, 0, ',', ' ') ?> F
            </div>
        </div>
    </div>

    <!-- Liste des commandes -->
    <div class="table-responsive">
        <table class="commande-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Date</th>
                    <th>Fournisseur</th>
                    <th>Articles</th>
                    <th class="text-right">Total</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($commandes)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="bx bx-package" style="font-size: 48px; color: #ccc;"></i>
                            <p>Aucune commande</p>
                            <a href="commande_ajout.php" style="color: #0b5e2e;">Creer une commande</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($commandes as $c): 
                        $details = getCommandeById($c['id']);
                        $nb_articles = count($details['articles'] ?? []);
                    ?>
                    <tr>
                        <td><strong><?= $c['reference'] ?? 'CMD-'.$c['id'] ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></td>
                        <td><?= htmlspecialchars($c['fournisseur_prenom'] . ' ' . $c['fournisseur_nom']) ?></td>
                        <td><?= $nb_articles ?> article(s)</td>
                        <td class="text-right"><?= number_format($c['total_global'], 0, ',', ' ') ?> F</td>
                        <td>
                            <span class="badge-statut badge-<?= $c['statut'] ?>">
                                <?= str_replace('_', ' ', ucfirst($c['statut'] ?? 'en_attente')) ?>
                            </span>
                        </td>
                        <td class="actions">
                            <a href="commande_details.php?id=<?= $c['id'] ?>" class="btn-action" title="Details">
                                <i class="bx bx-show"></i>
                            </a>
                            <?php if ($c['statut'] == 'en_attente'): ?>
                                <a href="commande_modifier.php?id=<?= $c['id'] ?>" class="btn-action" title="Modifier">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="../model/recevoirCommande.php?id=<?= $c['id'] ?>" 
                                   class="btn-action" 
                                   style="background: #4caf50;"
                                   onclick="return confirm('Marquer cette commande comme recue ?')"
                                   title="Reception">
                                    <i class="bx bx-package"></i>
                                </a>
                                <a href="../model/annulerCommande.php?id=<?= $c['id'] ?>" 
                                   class="btn-action" 
                                   style="background: #f44336;"
                                   onclick="return confirm('Annuler cette commande ?')"
                                   title="Annuler">
                                    <i class="bx bx-x-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'pied.php'; ?>