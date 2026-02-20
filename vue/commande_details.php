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

$devise = getConfig('devise', 'FCFA');
?>

<style>
.details-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
}

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

.btn-back {
    background: #f5f5f5;
    color: #666;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    border: 1px solid #ddd;
}

.btn-back:hover {
    background: #e8e8e8;
}

.details-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 25px;
}

.details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f5f5f5;
    flex-wrap: wrap;
    gap: 15px;
}

.details-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: #1a2b3c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.badge-statut {
    display: inline-block;
    padding: 6px 15px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 600;
}

.badge-en_attente {
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

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.info-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
}

.info-item .info-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.info-item .info-value {
    font-size: 16px;
    font-weight: 600;
    color: #1a2b3c;
}

.notes-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 25px;
}

.notes-box .notes-label {
    font-size: 13px;
    font-weight: 600;
    color: #666;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.notes-box .notes-content {
    color: #1a2b3c;
    line-height: 1.6;
    white-space: pre-line;
}

.table-responsive {
    overflow-x: auto;
    margin-top: 15px;
}

.details-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.details-table th {
    background: #0b5e2e;
    color: white;
    padding: 12px;
    text-align: left;
}

.details-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.details-table tbody tr:hover {
    background: #f8f9fa;
}

.details-table tfoot td {
    background: #f8f9fa;
    font-weight: 600;
    border-top: 2px solid #e0e0e0;
}

.text-right {
    text-align: right;
}

.action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
    flex-wrap: wrap;
}

.btn-action {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    border: none;
    cursor: pointer;
}

.btn-action.primary {
    background: #0b5e2e;
    color: white;
}

.btn-action.primary:hover {
    background: #0a4f26;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 94, 46, 0.2);
}

.btn-action.warning {
    background: #ff9800;
    color: white;
}

.btn-action.warning:hover {
    background: #f57c00;
    transform: translateY(-2px);
}

.btn-action.danger {
    background: #f44336;
    color: white;
}

.btn-action.danger:hover {
    background: #d32f2f;
    transform: translateY(-2px);
}

.btn-action.secondary {
    background: #f5f5f5;
    color: #666;
    border: 1px solid #ddd;
}

.btn-action.secondary:hover {
    background: #e8e8e8;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert.success {
    background: #e3f5e9;
    color: #0b5e2e;
    border-left: 4px solid #0b5e2e;
}

.alert.danger {
    background: #fee9e7;
    color: #b3261e;
    border-left: 4px solid #b3261e;
}

.alert.warning {
    background: #fff3e0;
    color: #e65100;
    border-left: 4px solid #ff9800;
}

/* Styles pour l'impression */
@media print {
    .sidebar,
    .home-section nav,
    .page-header .btn-back,
    .action-buttons,
    .alert,
    footer,
    .profile-details {
        display: none !important;
    }
    
    body {
        background: white;
        padding: 0;
        margin: 0;
    }
    
    .home-content {
        margin: 0 !important;
        padding: 20px !important;
        width: 100% !important;
    }
    
    .details-container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    .details-card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        padding: 20px !important;
    }
    
    .page-header h1 {
        font-size: 24px;
        color: #000 !important;
        margin-bottom: 20px;
    }
    
    .page-header h1 i {
        display: none;
    }
    
    .info-grid {
        break-inside: avoid;
    }
    
    .details-table th {
        background: #f0f0f0 !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .badge-statut {
        border: 1px solid #000 !important;
        background: white !important;
        color: #000 !important;
    }
    
    i {
        display: none !important;
    }
    
    .print-footer {
        display: block;
        text-align: center;
        margin-top: 30px;
        font-size: 12px;
        color: #666;
        border-top: 1px solid #ccc;
        padding-top: 10px;
    }
}

.print-footer {
    display: none;
}
</style>

<div class="home-content">
    <div class="details-container">
        
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?= $_SESSION['message']['type'] ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- En-tête -->
        <div class="page-header">
            <h1>
                <i class="bx bx-detail"></i>
                Details de la commande
            </h1>
            <a href="commande.php" class="btn-back">
                <i class="bx bx-arrow-back"></i> Retour
            </a>
        </div>

        <!-- Carte principale -->
        <div class="details-card">
            <div class="details-header">
                <h2>
                    <i class="bx bx-package"></i>
                    Commande <?= $commande['reference'] ?? 'CMD-'.$commande['id'] ?>
                </h2>
                <span class="badge-statut badge-<?= $commande['statut'] ?>">
                    <?= str_replace('_', ' ', ucfirst($commande['statut'] ?? 'en_attente')) ?>
                </span>
            </div>

            <!-- Grille d'informations -->
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-calendar"></i> Date commande
                    </div>
                    <div class="info-value">
                        <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-truck"></i> Livraison prevue
                    </div>
                    <div class="info-value">
                        <?= $commande['date_livraison_prevue'] ? date('d/m/Y', strtotime($commande['date_livraison_prevue'])) : 'Non definie' ?>
                    </div>
                </div>

                <?php if ($commande['date_livraison_reelle']): ?>
                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-check-circle"></i> Livraison reelle
                    </div>
                    <div class="info-value">
                        <?= date('d/m/Y', strtotime($commande['date_livraison_reelle'])) ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-user"></i> Fournisseur
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($commande['fournisseur_prenom'] . ' ' . $commande['fournisseur_nom']) ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-map"></i> Adresse
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($commande['adresse'] ?? 'Non renseignee') ?>
                    </div>
                </div>

                <?php if ($commande['id_utilisateur']): ?>
                <div class="info-item">
                    <div class="info-label">
                        <i class="bx bx-user-circle"></i> Cree par
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($commande['utilisateur_prenom'] . ' ' . $commande['utilisateur_nom']) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Notes -->
            <?php if (!empty($commande['notes'])): ?>
            <div class="notes-box">
                <div class="notes-label">
                    <i class="bx bx-note"></i> Notes
                </div>
                <div class="notes-content">
                    <?= nl2br(htmlspecialchars($commande['notes'])) ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tableau des articles -->
            <h3 style="margin: 20px 0 15px; display: flex; align-items: center; gap: 8px;">
                <i class="bx bx-list-ul"></i>
                Articles commandes
            </h3>

            <div class="table-responsive">
                <table class="details-table">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th class="text-right">Prix unitaire</th>
                            <th class="text-center">Quantite</th>
                            <th class="text-right">Total</th>
                            <?php if ($commande['statut'] == 'livree'): ?>
                            <th class="text-center">Recu</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_commande = 0;
                        foreach ($commande['articles'] as $article): 
                            $total_ligne = $article['quantite'] * $article['prix_unitaire'];
                            $total_commande += $total_ligne;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($article['nom_article']) ?></td>
                            <td class="text-right"><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> <?= $devise ?></td>
                            <td class="text-center"><?= $article['quantite'] ?></td>
                            <td class="text-right"><?= number_format($total_ligne, 0, ',', ' ') ?> <?= $devise ?></td>
                            <?php if ($commande['statut'] == 'livree'): ?>
                            <td class="text-center">
                                <?= $article['quantite_recue'] ?? $article['quantite'] ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?= $commande['statut'] == 'livree' ? 4 : 3 ?>" class="text-right">
                                <strong>Total commande</strong>
                            </td>
                            <td class="text-right">
                                <strong><?= number_format($total_commande, 0, ',', ' ') ?> <?= $devise ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Boutons d'action selon le statut -->
            <div class="action-buttons">
                <?php if ($commande['statut'] == 'en_attente'): ?>
                    <a href="commande_modifier.php?id=<?= $commande['id'] ?>" class="btn-action warning">
                        <i class="bx bx-edit"></i> Modifier
                    </a>
                    <a href="../model/recevoirCommande.php?id=<?= $commande['id'] ?>" 
                       class="btn-action primary"
                       onclick="return confirm('Marquer cette commande comme recue ? Le stock sera mis a jour.')">
                        <i class="bx bx-package"></i> Receptionner
                    </a>
                    <a href="../model/annulerCommande.php?id=<?= $commande['id'] ?>" 
                       class="btn-action danger"
                       onclick="return confirm('Annuler cette commande ?')">
                        <i class="bx bx-x-circle"></i> Annuler
                    </a>
                <?php elseif ($commande['statut'] == 'validee'): ?>
                    <a href="../model/recevoirCommande.php?id=<?= $commande['id'] ?>" 
                       class="btn-action primary"
                       onclick="return confirm('Marquer cette commande comme recue ? Le stock sera mis a jour.')">
                        <i class="bx bx-package"></i> Receptionner
                    </a>
                    <a href="../model/annulerCommande.php?id=<?= $commande['id'] ?>" 
                       class="btn-action danger"
                       onclick="return confirm('Annuler cette commande ?')">
                        <i class="bx bx-x-circle"></i> Annuler
                    </a>
                <?php endif; ?>
                
                <button onclick="imprimerFacture()" class="btn-action secondary">
                    <i class="bx bx-printer"></i> Imprimer la facture
                </button>
            </div>
        </div>

        <!-- Pied de page pour impression -->
        <div class="print-footer">
            Document imprime le <?= date('d/m/Y H:i') ?> - <?= htmlspecialchars(getConfig('entreprise_nom', 'EasyStock_Pro')) ?>
        </div>
    </div>
</div>

<!-- Contenu de la facture (cache a l'ecran mais utilise pour l'impression) -->
<div id="facture-content" style="display: none;">
    <div class="entete">
        <h1><?= htmlspecialchars(getConfig('entreprise_nom', 'EasyStock_Pro')) ?></h1>
        <p><?= htmlspecialchars(getConfig('entreprise_adresse', '')) ?></p>
        <p>Tel: <?= htmlspecialchars(getConfig('entreprise_telephone', '')) ?></p>
        <p>Email: <?= htmlspecialchars(getConfig('entreprise_email', '')) ?></p>
    </div>
    
    <div class="reference">
        BON DE COMMANDE N° <?= $commande['reference'] ?? 'CMD-'.$commande['id'] ?>
    </div>
    
    <div class="info-section">
        <div class="info-block">
            <h3>Fournisseur</h3>
            <p><strong><?= htmlspecialchars($commande['fournisseur_prenom'] . ' ' . $commande['fournisseur_nom']) ?></strong></p>
            <p><?= htmlspecialchars($commande['adresse'] ?? 'Adresse non renseignee') ?></p>
        </div>
        <div class="info-block">
            <h3>Informations commande</h3>
            <p><strong>Date:</strong> <?= date('d/m/Y H:i', strtotime($commande['date_commande'])) ?></p>
            <?php if ($commande['date_livraison_prevue']): ?>
            <p><strong>Livraison prevue:</strong> <?= date('d/m/Y', strtotime($commande['date_livraison_prevue'])) ?></p>
            <?php endif; ?>
            <?php if ($commande['date_livraison_reelle']): ?>
            <p><strong>Livraison reelle:</strong> <?= date('d/m/Y', strtotime($commande['date_livraison_reelle'])) ?></p>
            <?php endif; ?>
            <p><strong>Statut:</strong> <?= str_replace('_', ' ', ucfirst($commande['statut'])) ?></p>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Article</th>
                <th>Prix unitaire</th>
                <th>Quantite</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            foreach ($commande['articles'] as $article): 
                $sous_total = $article['quantite'] * $article['prix_unitaire'];
                $total += $sous_total;
            ?>
            <tr>
                <td><?= htmlspecialchars($article['nom_article']) ?></td>
                <td><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> <?= $devise ?></td>
                <td><?= $article['quantite'] ?></td>
                <td><?= number_format($sous_total, 0, ',', ' ') ?> <?= $devise ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="total">
        Total commande: <?= number_format($total, 0, ',', ' ') ?> <?= $devise ?>
    </div>
    
    <?php if (!empty($commande['notes'])): ?>
    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
        <strong>Notes:</strong><br>
        <?= nl2br(htmlspecialchars($commande['notes'])) ?>
    </div>
    <?php endif; ?>
    
    <div class="footer">
        Document genere le <?= date('d/m/Y H:i') ?> par <?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?><br>
        <?= htmlspecialchars(getConfig('entreprise_nom', 'EasyStock_Pro')) ?> - Votre partenaire stock
    </div>
</div>

<script>
function imprimerFacture() {
    var printWindow = window.open('', '_blank');
    
    var factureHTML = document.getElementById('facture-content').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Facture - Commande <?= $commande['reference'] ?? 'CMD-'.$commande['id'] ?></title>
            <style>
                body {
                    font-family: 'Poppins', Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background: white;
                }
                .facture {
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 30px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                }
                .entete {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 2px solid #0b5e2e;
                }
                .entete h1 {
                    color: #0b5e2e;
                    margin: 10px 0 5px;
                }
                .entete p {
                    color: #666;
                    margin: 5px 0;
                }
                .info-section {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 30px;
                    padding: 20px;
                    background: #f8f9fa;
                    border-radius: 8px;
                }
                .info-block h3 {
                    color: #0b5e2e;
                    margin: 0 0 10px 0;
                    font-size: 16px;
                }
                .info-block p {
                    margin: 5px 0;
                    color: #333;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                }
                th {
                    background: #0b5e2e;
                    color: white;
                    padding: 12px;
                    text-align: left;
                }
                td {
                    padding: 10px;
                    border-bottom: 1px solid #eee;
                }
                .total {
                    text-align: right;
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 20px;
                    padding-top: 20px;
                    border-top: 2px solid #0b5e2e;
                }
                .footer {
                    text-align: center;
                    margin-top: 30px;
                    padding-top: 20px;
                    border-top: 1px solid #ddd;
                    color: #666;
                    font-size: 12px;
                }
                .reference {
                    font-size: 20px;
                    font-weight: bold;
                    color: #0b5e2e;
                    margin-bottom: 20px;
                }
                @media print {
                    body { padding: 0; }
                    .facture { border: none; }
                }
            </style>
        </head>
        <body>
            <div class="facture">
                ${factureHTML}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    printWindow.onload = function() {
        printWindow.print();
    };
}
</script>

<?php include 'pied.php'; ?>