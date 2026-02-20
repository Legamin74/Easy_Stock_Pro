<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAdmin();

$utilisateurs = getUtilisateursArchive();
?>

<div class="home-content">
    <!-- ======================================== -->
    <!--         EN-TÊTE AVEC ONGLETS              -->
    <!-- ======================================== -->
    <div class="page-header">
        <h2><i class="bx bx-archive"></i> Gestion des utilisateurs</h2>
        <div class="header-tabs">
            <a href="utilisateur.php" class="tab-link"> Actifs</a>
            <a href="utilisateur_archive.php" class="tab-link active"> Archives</a>
        </div>
    </div>

    <!-- ======================================== -->
    <!--         MESSAGES DE CONFIRMATION         -->
    <!-- ======================================== -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert-message <?= $_SESSION['message']['type'] ?>">
            <i class="bx <?= $_SESSION['message']['type'] == 'success' ? 'bx-check-circle' : 'bx-error-circle' ?>"></i>
            <span><?= $_SESSION['message']['text'] ?></span>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- ======================================== -->
    <!--      SECTION LISTE DES UTILISATEURS      -->
    <!-- ======================================== -->
    <div class="list-section">
        <h3 class="list-title">
            <i class="bx bx-archive"></i>
            Utilisateurs archivés
        </h3>

        <div class="table-responsive">
            <table class="table utilisateur-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Login</th>
                        <th>Rôle</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($utilisateurs)): ?>
                        <tr>
                            <td colspan="7" class="empty-message">
                                <i class="bx bx-archive"></i>
                                <p>Aucun utilisateur archivé</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($utilisateurs as $u): ?>
                        <tr>
                            <td><strong>#<?= $u['id'] ?></strong></td>
                            <td><?= htmlspecialchars($u['prenom'] . ' ' . $u['nom']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['telephone'] ?? '—') ?></td>
                            <td><?= htmlspecialchars($u['login']) ?></td>
                            <td>
                                <span class="role-badge <?= $u['role'] ?>">
                                    <?= ucfirst($u['role']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <a href="../model/restaurerUtilisateur.php?id=<?= $u['id'] ?>" 
                                   class="btn-icon restore" 
                                   onclick="return confirm('Restaurer cet utilisateur ?')"
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