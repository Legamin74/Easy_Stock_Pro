<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

$fournisseurs =  getFournisseurArchive() ;
?>

<div class="home-content">
   
    <div class="page-header">
        <h2> Gestion des fournisseurs</h2>
        <div class="header-tabs">
            <a href="fournisseur.php" class="tab-link"> Actifs</a>
            <a href="fournisseur_archive.php" class="tab-link active"> Archives</a>
        </div>
    </div>

    
    <div class="list-section">
        <h3 class="list-title">
            <i class="bx bx-archive"></i>
            fournisseurs archivés
        </h3>

        <div class="table-responsive">
            <table class="client-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Téléphone</th>
                        <th>Adresse</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fournisseurs)): ?>
                        <tr>
                            <td colspan="5" class="empty-message">
                                <i class="bx bx-archive"></i>
                                <p>Aucun fournisseur archivé</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fournisseurs as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nom']) ?></td>
                            <td><?= htmlspecialchars($c['prenom']) ?></td>
                            <td><?= htmlspecialchars($c['telephone']) ?></td>
                            <td><?= htmlspecialchars($c['adresse'] ?? '—') ?></td>
                            <td class="actions">
                                <a href="../model/restaurerFournisseur.php?id=<?= $c['id'] ?>" 
                                   class="btn-icon restore" 
                                   onclick="return confirm('Restaurer ce fournisseur ?')"
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