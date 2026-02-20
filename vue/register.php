<?php
session_start();
require_once '../model/fonction.php';

// Rediriger si déjà connecté
if (estConnecte()) {
    header('Location: dashboard.php');
    exit;
}

// Vérifier si un admin existe déjà
$admin_existe = verifierAdminExiste();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'] ?? null;
    $login = $_POST['login'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $erreur = null;
    
    if ($password !== $confirm_password) {
        $erreur = "Les mots de passe ne correspondent pas";
    } elseif (strlen($password) < 6) {
        $erreur = "Le mot de passe doit contenir au moins 6 caractères";
    } elseif (loginExiste($login)) {
        $erreur = "Ce nom d'utilisateur est déjà pris";
    } elseif (emailExiste($email)) {
        $erreur = "Cet email est déjà utilisé";
    } else {
        // Créer l'administrateur
        $data = [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'login' => $login,
            'password' => $password,
            'role' => 'admin'
        ];
        
        $id = creerUtilisateur($data);
        
        if ($id) {
            // Connecter automatiquement
            $user = verifierConnexion($login, $password);
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
        } else {
            $erreur = "Erreur lors de la création du compte";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte admin - EasyStock_Pro</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            background: linear-gradient(135deg, rgb(1, 62, 1) 0%, rgb(6, 88, 6) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header img {
            width: 150px;
            margin-bottom: 20px;
        }
        .register-header h2 {
            color: rgb(1, 62, 1);
            margin-bottom: 5px;
        }
        .register-header p {
            color: #666;
        }
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group.full-width {
            grid-column: span 2;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        .input-group {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 5px 15px;
            transition: 0.3s;
            background: white;
        }
        .input-group:focus-within {
            border-color: rgb(6, 88, 6);
            box-shadow: 0 0 0 3px rgba(6, 88, 6, 0.1);
        }
        .input-group i {
            color: #999;
            font-size: 18px;
        }
        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: none;
            outline: none;
            font-size: 15px;
            background: transparent;
        }
        .btn-register {
            width: 100%;
            background: rgb(6, 88, 6);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-register:hover {
            background: rgb(1, 62, 1);
            transform: translateY(-2px);
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: white;
            background: #f44336;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert i {
            font-size: 20px;
        }
        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }
        .login-link a {
            color: rgb(6, 88, 6);
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .info-box {
            background: #e8f5e9;
            border-radius: 10px;
            padding: 15px;
            margin-top: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgb(1, 62, 1);
        }
        .info-box i {
            font-size: 24px;
        }
        @media (max-width: 600px) {
            .register-container {
                padding: 25px;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
            .form-group.full-width {
                grid-column: span 1;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <img src="../public/img/logo-removebg-preview.png" alt="EasyStock_Pro">
            <h2>EasyStock_Pro</h2>
            <p>Configuration initiale</p>
        </div>

        <?php if ($admin_existe): ?>
            <div class="alert" style="background: #ff9800;">
                <i class="bx bx-info-circle"></i>
                Un administrateur existe déjà. 
                <a href="login.php" style="color: white; font-weight: bold; margin-left: 5px;">Connectez-vous</a>
            </div>
        <?php else: ?>

            <?php if (!empty($erreur)): ?>
                <div class="alert">
                    <i class="bx bx-error-circle"></i>
                    <?= $erreur ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nom</label>
                        <div class="input-group">
                            <i class="bx bx-user"></i>
                            <input type="text" name="nom" placeholder="Votre nom" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Prénom</label>
                        <div class="input-group">
                            <i class="bx bx-user"></i>
                            <input type="text" name="prenom" placeholder="Votre prénom" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Email</label>
                        <div class="input-group">
                            <i class="bx bx-envelope"></i>
                            <input type="email" name="email" placeholder="exemple@email.com" required>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Téléphone (optionnel)</label>
                        <div class="input-group">
                            <i class="bx bx-phone"></i>
                            <input type="tel" name="telephone" placeholder="77 123 45 67">
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label>Nom d'utilisateur</label>
                        <div class="input-group">
                            <i class="bx bx-id-card"></i>
                            <input type="text" name="login" placeholder="admin1" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mot de passe</label>
                        <div class="input-group">
                            <i class="bx bx-lock"></i>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Confirmer</label>
                        <div class="input-group">
                            <i class="bx bx-lock-alt"></i>
                            <input type="password" name="confirm_password" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="bx bx-check-shield"></i>
                    Créer mon compte administrateur
                </button>
            </form>

            <div class="info-box">
                <i class="bx bx-info-circle"></i>
                <div>
                    <strong>Première connexion ?</strong><br>
                    Ce formulaire crée le premier compte administrateur.
                    Vous pourrez ensuite créer des gestionnaires et caissiers.
                </div>
            </div>

            <div class="login-link">
                Déjà un compte ? <a href="login.php">Se connecter</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>