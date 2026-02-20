<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom_article'];
    $id_categorie = $_POST['id_categorie'] ?: null;
    $quantite = $_POST['quantite'];
    $prix = $_POST['prix_unitaire'];
    $seuil = $_POST['seuil_alerte'] ?? 5;
    $expiration = !empty($_POST['date_expiration']) ? $_POST['date_expiration'] : null;

    $sql = "UPDATE article SET 
                nom_article = ?, 
                id_categorie = ?, 
                quantite = ?, 
                prix_unitaire = ?, 
                seuil_alerte = ?, 
                date_expiration = ? 
            WHERE id = ?";
    
    $req = $connexion->prepare($sql);
    $result = $req->execute([$nom, $id_categorie, $quantite, $prix, $seuil, $expiration, $id]);

    if ($result) {
        $_SESSION['message'] = [
            'text' => ' Article modifié avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur lors de la modification',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/article.php');
exit;