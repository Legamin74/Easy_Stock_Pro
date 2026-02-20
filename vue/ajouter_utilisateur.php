<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAdmin(); //  SEUL L'ADMIN PEUT CRÉER

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nom' => $_POST['nom'],
        'prenom' => $_POST['prenom'],
        'email' => $_POST['email'],
        'telephone' => $_POST['telephone'],
        'login' => $_POST['login'],
        'password' => $_POST['password'],
        'role' => $_POST['role']
    ];
    
    $confirm = $_POST['confirm_password'];
    $erreur = null;
    
    if ($data['password'] !== $confirm) {
        $erreur = "Les mots de passe ne correspondent pas";
    } elseif (strlen($data['password']) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères";
    } elseif (loginExiste($data['login'])) {
        $erreur = "Ce nom d'utilisateur est déjà pris";
    } elseif (emailExiste($data['email'])) {
        $erreur = "Cet email est déjà utilisé";
    } else {
        $id = creerUtilisateur($data);
        if ($id) {
            $_SESSION['message'] = [
                'text' => " Utilisateur créé avec succès",
                'type' => 'success'
            ];
         ?>
<script>
    window.location.href = 'utilisateur.php';
</script>
<?php
exit;
        }
    }
}
?>

<div class="home-content">
    <div class="form-container" style="max-width: 600px;">
        <div class="form-header">
            <i class="bx bx-user-plus"></i>
            <h2>Nouvel utilisateur</h2>
        </div>

        <?php if (!empty($erreur)): ?>
            <div class="alert danger"><?= $erreur ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-grid" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" required>
                </div>
                <div class="form-group full-width">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group full-width">
                    <label>Téléphone</label>
                    <input type="text" name="telephone">
                </div>
                <div class="form-group full-width">
                    <label>Rôle</label>
                    <select name="role" required>
                        <option value="">Sélectionner</option>
                        <option value="gestionnaire">Gestionnaire</option>
                        <option value="caissier">Caissier</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Nom d'utilisateur</label>
                    <input type="text" name="login" required>
                </div>
                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirmer</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    <i class="bx bx-check"></i>
                    Créer
                </button>
                <a href="utilisateur.php" class="btn-cancel">
                    <i class="bx bx-x"></i>
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<?php include 'pied.php'; ?>