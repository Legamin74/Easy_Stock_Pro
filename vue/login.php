<?php
session_start();
require_once '../model/fonction.php';

// Rediriger si déjà connecté
if (estConnecte()) {
    header('Location: dashboard.php');
    exit;
}

$erreur = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $user = verifierConnexion($login, $password);
    
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $erreur = "Nom d'utilisateur ou mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - EasyStock_Pro</title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        body {
           background: linear-gradient(135deg, rgb(1, 62, 1) 0%, rgb(6, 88, 6) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Poppins", sans-serif;
            margin: 0;
        }
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header img {
            width: 150px;
            margin-bottom: 20px;
        }
        .login-header h2 {
            color: rgb(1, 62, 1);
            margin-bottom: 5px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .input-group {
            display: flex;
            align-items: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 5px 15px;
            transition: 0.3s;
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
        }
        .btn-login {
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
        .btn-login:hover {
            background: rgb(1, 62, 1);
            transform: translateY(-2px);
        }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: white;
            background: #f44336;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .register-link a {
            color: rgb(6, 88, 6);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../public/img/logo-removebg-preview.png" alt="EasyStock_Pro">
            <h2>EasyStock_Pro</h2>
            <p>Connectez-vous à votre espace</p>
        </div>

        <?php if ($erreur): ?>
            <div class="alert">
                <i class="bx bx-error-circle"></i>
                <?= $erreur ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Nom d'utilisateur</label>
                <div class="input-group">
                    <i class="bx bx-user"></i>
                    <input type="text" name="login" placeholder="Entrez votre login" required>
                </div>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <div class="input-group">
                    <i class="bx bx-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bx bx-log-in"></i>
                Se connecter
            </button>
            
        </form>
        

        <div class="register-link">
             <a href="register.php">Créer un compte admin</a>
        </div>
        <div style="text-align: center; margin-top: 15px;">
            <a href="mot_de_passe_oublie.php" style="color: rgb(1, 62, 1); text-decoration: none;">
                Mot de passe oublié ?
            </a>
        </div>
    </div>
    
</body>
</html>