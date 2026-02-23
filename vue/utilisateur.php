<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAdmin(); //  SEUL L'ADMIN PEUT VOIR

//  Sécurisation : uniquement les admins

$utilisateurs = getUtilisateursActifs();
?>

<div class="home-content">
   
    <div class="page-header">
        <h2> Gestion des utilisateur</h2>
         <a href="ajouter_utilisateur.php" class="btn-add">
            <i class="bx bx-plus"></i> Nouvelle commande
        </a>
        <div class="header-tabs">
            <a href="utilisateur.php" class="tab-link active"> Actifs</a>
            <a href="utilisateur_archive.php" class="tab-link"> Archives</a>
        </div>
    </div>
    

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
    <?php unset($_SESSION['message']); endif; ?>

    <div class="table-responsive">
        <table class="mtable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Login</th>
                    <th>Rôle</th>
                    <th>Dernière connexion</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($utilisateurs as $user): ?>
                <tr>
                    <td>#<?= $user['id'] ?></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="bx bxs-user-circle" style="font-size: 24px; color: rgb(6, 88, 6);"></i>
                            <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge-role admin">
                                <i class="bx bx-shield"></i> Admin
                            </span>
                        <?php elseif ($user['role'] === 'gestionnaire'): ?>
                            <span class="badge-role gestionnaire">
                                <i class="bx bx-briefcase"></i> Gestionnaire
                            </span>
                        <?php else: ?>
                            <span class="badge-role caissier">
                                <i class="bx bx-user"></i> Caissier
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $user['derniere_connexion'] ? date('d/m/Y H:i', strtotime($user['derniere_connexion'])) : 'Jamais' ?>
                    </td>
                    <td class="actions">
                        <a href="profil.php?id=<?= $user['id'] ?>" class="btn-view" title="Voir profil">
                            <i class="bx bx-show"></i>
                        </a>
                        <?php if ($user['id'] != $_SESSION['user']['id']): ?>
                            <a href="../model/archiverUtilisateur.php?id=<?= $user['id'] ?>" 
                               class="btn-archive" 
                               title="Archiver"
                               onclick="return confirm('Archiver cet utilisateur ?')">
                                <i class="bx bx-archive"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'pied.php'; ?>