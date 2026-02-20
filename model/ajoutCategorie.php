<?php
session_start();
require_once 'connexion.php';

if (!empty($_POST['libelle_categorie'])) {

    $sql = "INSERT INTO categorie_article (libelle_categorie) VALUES (?)";
    $req = $connexion->prepare($sql);
    $req->execute([trim($_POST['libelle_categorie'])]);

    $_SESSION['message'] = [
        'text' => 'Catégorie ajoutée avec succès',
        'type' => 'success'
    ];

} else {
    $_SESSION['message'] = [
        'text' => 'Veuillez saisir une catégorie',
        'type' => 'danger'
    ];
}

header('Location: ../vue/categorie.php');
exit;
