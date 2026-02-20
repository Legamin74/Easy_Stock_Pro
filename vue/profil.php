<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth(); //  TOUS LES CONNECTÉS

$user = $_SESSION['user'];
$message = null;
$type = '';

// Changer login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $nouveau = $_POST['nouveau_login'];
    if (modifierMonLogin($user['id'], $nouveau)) {
        $_SESSION['user']['login'] = $nouveau;
        $message = " Login modifié";
        $type = 'success';
    } else {
        $message = " Login déjà utilisé";
        $type = 'danger';
    }
}

// Changer mot de passe
if (isset($_POST['action']) && $_POST['action'] === 'password') {
    $ancien = $_POST['ancien'];
    $nouveau = $_POST['nouveau'];
    $confirm = $_POST['confirm'];
    
    if ($nouveau !== $confirm) {
        $message = " Mots de passe différents";
        $type = 'danger';
    } elseif (strlen($nouveau) < 6) {
        $message = " 6 caractères minimum";
        $type = 'danger';
    } else {
        if (modifierMonMotDePasse($user['id'], $ancien, $nouveau)) {
            $message = " Mot de passe modifié";
            $type = 'success';
        } else {
            $message = " Ancien mot de passe incorrect";
            $type = 'danger';
        }
    }
}
?>

<div class="home-content">
    <div class="form-container" style="max-width: 500px;">
        <div class="form-header">
            <i class="bx bx-user-circle"></i>
            <h2>Mon profil</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $type ?>"><?= $message ?></div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 30px;">
            <i class="bx bxs-user-circle" style="font-size: 80px; color: rgb(6, 88, 6);"></i>
            <h3><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h3>
            <p style="color: #666;"><?= $user['email'] ?></p>
        </div>

        <!-- Changer login -->
        <form method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
            <h4><i class="bx bx-id-card"></i> Changer login</h4>
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Login actuel</label>
                <input type="text" value="<?= $user['login'] ?>" readonly>
            </div>
            <div class="form-group">
                <label>Nouveau login</label>
                <input type="text" name="nouveau_login" required>
            </div>
            <button type="submit" class="btn-submit" style="width: 100%;">Modifier</button>
        </form>

        <!-- Changer mot de passe -->
        <form method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 10px;">
            <h4><i class="bx bx-lock"></i> Changer mot de passe</h4>
            <input type="hidden" name="action" value="password">
            <div class="form-group">
                <label>Ancien mot de passe</label>
                <input type="password" name="ancien" required>
            </div>
            <div class="form-group">
                <label>Nouveau mot de passe</label>
                <input type="password" name="nouveau" required>
            </div>
            <div class="form-group">
                <label>Confirmer</label>
                <input type="password" name="confirm" required>
            </div>
            <button type="submit" class="btn-submit" style="width: 100%;">Modifier</button>
        </form>
    </div>
</div>

<?php include 'pied.php'; ?>