<?php
require_once '../model/fonction.php';

if (empty($_GET['id_vente'])) {
    die("Vente introuvable");
}

$vente = getVenteById($_GET['id_vente']);
if (!$vente) {
    die("Reçu introuvable");
}

// Marquer comme imprimé si ce n'est pas déjà fait
if (!$vente['imprime']) {
    marquerVenteImprimee($_GET['id_vente']);
    $vente['imprime'] = 1;
    $vente['date_impression'] = date('Y-m-d H:i:s');
}

// Récupération configuration
$nom_entreprise = getConfig('entreprise_nom', 'EasyStock_Pro');
$adresse = getConfig('entreprise_adresse', 'Dakar, Sénégal');
$telephone = getConfig('entreprise_telephone', '77 123 45 67');
$email = getConfig('entreprise_email', 'contact@easystock-pro.com');
$devise = getConfig('devise', 'FCFA');
$format_recu = getConfig('format_recu', 'ESP-{annee}-{numero}');
$numero_recu = formatNumeroRecu($vente['id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu - <?= htmlspecialchars($nom_entreprise) ?></title>
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* ================= STYLE DIRECT DU REÇU ================= */
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .recu {
            width: 380px;
            background: white;
            margin: 0 auto;
            padding: 25px 20px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e0e0e0;
        }

        .logo {
            width: 80px;
            display: block;
            margin: 0 auto 15px;
        }

        h1 {
            text-align: center;
            color: rgb(1, 62, 1);
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .slogan {
            text-align: center;
            font-size: 13px;
            color: #666;
            margin-bottom: 20px;
            font-style: italic;
        }

        .entreprise-info {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-bottom: 20px;
            line-height: 1.6;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
        }

        .entreprise-info p {
            margin: 3px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .entreprise-info i {
            color: rgb(1, 62, 1);
            font-size: 14px;
        }

        hr {
            border: none;
            border-top: 1px dashed #ccc;
            margin: 15px 0;
        }

        .numero-recu {
            text-align: center;
            font-size: 14px;
            background: #e8f5e9;
            padding: 10px;
            border-radius: 20px;
            margin: 15px 0;
            font-weight: 500;
            color: rgb(1, 62, 1);
            border: 1px solid rgba(1, 62, 1, 0.2);
        }

        .client-info {
            font-size: 13px;
            line-height: 1.8;
            margin-bottom: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .client-info p {
            margin: 5px 0;
            display: flex;
        }

        .client-info strong {
            width: 70px;
            color: #333;
        }

        .date-impression {
            font-size: 12px;
            color: #666;
            text-align: right;
            margin-bottom: 15px;
            font-style: italic;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 13px;
        }

        th {
            background: rgb(1, 62, 1);
            color: white;
            padding: 10px 5px;
            font-weight: 500;
            text-align: center;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tfoot td {
            border-bottom: none;
            padding-top: 15px;
            font-weight: bold;
            color: rgb(1, 62, 1);
        }

        .merci {
            text-align: center;
            margin: 20px 0 15px;
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .merci span {
            display: block;
            margin-top: 5px;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-print {
            flex: 1;
            background: rgb(1, 62, 1);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-close {
            flex: 0.5;
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: 0.3s;
        }

        .btn-print:hover {
            background: rgb(0, 80, 0);
            transform: translateY(-2px);
        }

        .btn-close:hover {
            background: #e0e0e0;
        }

        /* ================= STYLE POUR L'IMPRESSION ================= */
        @media print {
            body {
                background: white;
                padding: 0;
                display: block;
            }
            
            .recu {
                width: 100%;
                max-width: 380px;
                margin: 0 auto;
                box-shadow: none;
                border: none;
                border-radius: 0;
                padding: 15px;
            }
            
            .buttons {
                display: none !important;
            }
            
            .btn-print, .btn-close {
                display: none !important;
            }
            
            .entreprise-info {
                background: none;
                padding: 5px;
            }
            
            .numero-recu {
                background: none;
                border: 1px solid #ccc;
            }
            
            .client-info {
                background: none;
            }
            
            @page {
                margin: 1cm;
            }
        }
    </style>
</head>
<body>
    <div class="recu">
        <!-- Logo -->
        <img src="../public/img/logo-removebg-preview.png" alt="<?= htmlspecialchars($nom_entreprise) ?>" class="logo">
        
        <!-- Nom entreprise -->
        <h1><?= htmlspecialchars($nom_entreprise) ?></h1>
        <div class="slogan">Votre partenaire stock</div>
        
        <!-- Coordonnées -->
        <div class="entreprise-info">
            <p><i class="bx bx-map"></i> <?= htmlspecialchars($adresse) ?></p>
            <p><i class="bx bx-phone"></i> <?= htmlspecialchars($telephone) ?></p>
            <p><i class="bx bx-envelope"></i> <?= htmlspecialchars($email) ?></p>
        </div>

        <hr>

        <!-- N° reçu -->
        <p class="numero-recu"><strong>N° reçu :</strong> <?= $numero_recu ?></p>
        
        <!-- Client -->
        <div class="client-info">
            <p><strong>Client :</strong> <?= htmlspecialchars($vente['client_prenom'] . ' ' . $vente['client_nom']) ?></p>
            <p><strong>Tél :</strong> <?= htmlspecialchars($vente['telephone'] ?? 'Non renseigné') ?></p>
            <p><strong>Adresse :</strong> <?= htmlspecialchars($vente['adresse'] ?? 'Non renseignée') ?></p>
        </div>
        
        <!-- Date impression -->
        <p class="date-impression">
            <strong>Date d'impression :</strong>
            <?= date('d/m/Y H:i', strtotime($vente['date_impression'])) ?>
        </p>

        <!-- Tableau des articles -->
        <table>
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Qté</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vente['articles'] as $article): ?>
                <tr>
                    <td><?= htmlspecialchars($article['nom_article']) ?></td>
                    <td class="text-center"><?= $article['quantite'] ?></td>
                    <td class="text-right"><?= number_format($article['prix_unitaire'], 0, ',', ' ') ?> <?= $devise ?></td>
                    <td class="text-right"><?= number_format($article['total_ligne'], 0, ',', ' ') ?> <?= $devise ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total à payer :</strong></td>
                    <td class="text-right"><strong><?= number_format($vente['total_global'], 0, ',', ' ') ?> <?= $devise ?></strong></td>
                </tr>
            </tfoot>
        </table>

        <!-- Merci -->
        <div class="merci">
            Merci pour votre confiance !<br>
            <span style="font-size: 11px; color: #999;"><?= htmlspecialchars($nom_entreprise) ?></span>
        </div>

        <!-- Boutons -->
        <div class="buttons">
            <button onclick="window.print()" class="btn-print">
                Imprimer
            </button>
            <button onclick="window.close()" class="btn-close">
                Fermer
            </button>
        </div>
    </div>
</body>
</html>