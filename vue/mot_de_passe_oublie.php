<?php
session_start();
require_once '../model/fonction.php';

$step = $_GET['step'] ?? 'demande';
$email = $_SESSION['reset_email'] ?? '';
$message = null;
$type = null;

// ÉTAPE 1 : Demande de code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['demande'])) {
    $email = $_POST['email'];
    $resultat = demanderCodeRecuperation($email);
    
    if ($resultat['success']) {
        $_SESSION['reset_email'] = $email;
        $step = 'verification';
        $message = " Code envoyé à $email";
        $type = 'success';
        $debug_code = $resultat['code_debug'] ?? '';
    } else {
        $message = $resultat['message'];
        $type = 'danger';
    }
}

// ÉTAPE 2 : Vérification du code
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verifier'])) {
    $code = $_POST['code'];
    $email = $_SESSION['reset_email'];
    
    $resultat = verifierCodeRecuperation($email, $code);
    
    if ($resultat['success']) {
        $step = 'nouveau_mot_de_passe';
        $message = " Code valide. Choisissez un nouveau mot de passe.";
        $type = 'success';
    } else {
        $message = $resultat['message'];
        $type = 'danger';
    }
}

// ÉTAPE 3 : Nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reinitialiser'])) {
    $email = $_SESSION['reset_email'];
    $nouveau = $_POST['nouveau_password'];
    $confirm = $_POST['confirm_password'];
    
    if ($nouveau !== $confirm) {
        $message = " Les mots de passe ne correspondent pas";
        $type = 'danger';
    } elseif (strlen($nouveau) < 6) {
        $message = " Le mot de passe doit contenir au moins 6 caractères";
        $type = 'danger';
    } else {
        $resultat = reinitialiserMotDePasse($email, $nouveau);
        
        if ($resultat) {
            $message = " Mot de passe réinitialisé ! Vous pouvez vous connecter.";
            $type = 'success';
            $step = 'termine';
            unset($_SESSION['reset_email']);
        } else {
            $message = " Erreur lors de la réinitialisation";
            $type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - EasyStock_Pro</title>
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
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            width: 120px;
            margin-bottom: 20px;
        }
        .header h2 {
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
        .btn {
            width: 100%;
            background: rgb(6, 88, 6);
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background: rgb(1, 62, 1);
            transform: translateY(-2px);
        }
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            color: white;
        }
        .alert.success {
            background: #2bd47d;
        }
        .alert.danger {
            background: #f44336;
        }
        .back-link {
            text-align: center;
            margin-top: 25px;
        }
        .back-link a {
            color: rgb(6, 88, 6);
            text-decoration: none;
            font-weight: 600;
        }
        .code-debug {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-size: 24px;
            text-align: center;
            font-weight: bold;
            letter-spacing: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../public/img/logo-removebg-preview.png" alt="EasyStock_Pro">
            <h2>Mot de passe oublié ?</h2>
        </div>

        <?php if ($message): ?>
            <div class="alert <?= $type ?>"><?= $message ?></div>
        <?php endif; ?>

        <?php if (isset($debug_code) && $step == 'verification'): ?>
            <div class="code-debug"> CODE : <?= $debug_code ?></div>
        <?php endif; ?>

        <?php if ($step == 'demande'): ?>
            <form method="POST">
                <div class="form-group">
                    <label> Votre email</label>
                    <div class="input-group">
                        <i class="bx bx-envelope"></i>
                        <input type="email" name="email" placeholder="exemple@email.com" required>
                    </div>
                </div>
                <button type="submit" name="demande" class="btn">Envoyer le code</button>
            </form>

        <?php elseif ($step == 'verification'): ?>
            <form method="POST">
                <div class="form-group">
                    <label> Code à 6 chiffres</label>
                    <div class="input-group">
                        <i class="bx bx-lock"></i>
                        <input type="text" name="code" placeholder="123456" maxlength="6" required>
                    </div>
                </div>
                <button type="submit" name="verifier" class="btn">Vérifier</button>
            </form>

        <?php elseif ($step == 'nouveau_mot_de_passe'): ?>
            <form method="POST">
                <div class="form-group">
                    <label> Nouveau mot de passe</label>
                    <div class="input-group">
                        <i class="bx bx-lock"></i>
                        <input type="password" name="nouveau_password" placeholder="••••••••" required minlength="6">
                    </div>
                </div>
                <div class="form-group">
                    <label> Confirmer</label>
                    <div class="input-group">
                        <i class="bx bx-lock-alt"></i>
                        <input type="password" name="confirm_password" placeholder="••••••••" required minlength="6">
                    </div>
                </div>
                <button type="submit" name="reinitialiser" class="btn">Réinitialiser</button>
            </form>

        <?php elseif ($step == 'termine'): ?>
            <div style="text-align: center; padding: 20px;">
                <i class="bx bx-check-circle" style="font-size: 80px; color: #2bd47d;"></i>
                
                <a href="login.php" class="btn">Se connecter</a>
            </div>
        <?php endif; ?>

        <div class="back-link">
            <a href="login.php"> Retour à la connexion</a>
        </div>
    </div>
</body>
</html>