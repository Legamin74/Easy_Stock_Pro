<?php
include 'entete.php';

require_once '../model/fonction.php';
requireAuth();
 if (isset($_SESSION['message'])): ?>
    <div class="alert <?= $_SESSION['message']['type'] ?>">
        <?= $_SESSION['message']['text'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; 

// Récupération des clients actifs
$clients = getClientActif();
?>


<div class="home-content">
   
    <div class="page-header">
        <h2> Gestion des clients</h2>
        <div class="header-tabs">
            <a href="client.php" class="tab-link active"> Actifs</a>
            <a href="client_archive.php" class="tab-link"> Archives</a>
        </div>
    </div>

<div class="form-section">
    <div class="form-card">
        <h3 class="form-title">
            <i class="bx bx-user-plus"></i> 
            Ajouter un nouveau client
        </h3>

        <form action="../model/ajoutClient.php" method="POST" class="client-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" placeholder="Ex: Nikiema" required>
                </div>

                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" placeholder="Ex: Zakaria" required>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="tel" name="telephone" placeholder="Ex: 54846780" required>
                </div>

                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" placeholder="Ex: Ouaga 2000">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bx bx-save"></i> Enregistrer le client
                </button>
            </div>
        </form>
    </div>
</div>

   
    <div class="list-section">
        <h3 class="list-title">
            <i class="bx bx-list-ul"></i>
            Liste des clients actifs
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
                    <?php if (empty($clients)): ?>
                        <tr>
                            <td colspan="5" class="empty-message">
                                <i class="bx bx-user-x"></i>
                                <p>Aucun client actif</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clients as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['nom']) ?></td>
                            <td><?= htmlspecialchars($c['prenom']) ?></td>
                            <td><?= htmlspecialchars($c['telephone']) ?></td>
                            <td><?= htmlspecialchars($c['adresse'] ?? '—') ?></td>
                            <td class="actions">
                                <a href="modifier_client.php?id=<?= $c['id'] ?>" 
                                   class="btn-icon edit" 
                                   title="Modifier">
                                    <i class="bx bx-edit"></i>
                                </a>
                                <a href="../model/archiverClient.php?id=<?= $c['id'] ?>" 
                                   class="btn-icon archive" 
                                   onclick="return confirm('Archiver ce client ?')"
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