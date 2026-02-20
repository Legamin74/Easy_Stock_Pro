<?php
require_once 'connexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $nom = $_POST['nom_article'];
    $id_categorie = !empty($_POST['id_categorie']) ? $_POST['id_categorie'] : null;
    $quantite = (int)$_POST['quantite'];
    $prix = (int)$_POST['prix_unitaire'];
    $seuil = (int)($_POST['seuil_alerte'] ?? 5);
    
    // Gestion de la date d'expiration
    $date_expiration = null;
    if (!empty($_POST['date_expiration'])) {
        $date_expiration = $_POST['date_expiration'];
    }

    // Requête SQL
    $sql = "INSERT INTO article (nom_article, id_categorie, quantite, prix_unitaire, seuil_alerte, date_expiration) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $req = $connexion->prepare($sql);
    
    if ($req) {
        $result = $req->execute([$nom, $id_categorie, $quantite, $prix, $seuil, $date_expiration]);

        if ($result) {
            $_SESSION['message'] = [
                'text' => ' Article ajouté avec succès',
                'type' => 'success'
            ];
        } else {
            $_SESSION['message'] = [
                'text' => ' Erreur lors de l\'ajout',
                'type' => 'danger'
            ];
        }
    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur de préparation de la requête',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/article.php');
exit;