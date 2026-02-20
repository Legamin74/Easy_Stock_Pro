<?php
session_start();
require_once 'connexion.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['id']) &&
    !empty($_POST['libelle_categorie'])
) {
    $sql = "UPDATE categorie_article SET libelle_categorie = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([
        trim($_POST['libelle_categorie']),
        $_POST['id']
    ]);

    if ($req->rowCount() > 0) {
        $_SESSION['message'] = [
            'text' => 'Catégorie modifiée avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => 'Aucune modification effectuée ou une erreur est survenue',
            'type' => 'danger'
        ];
    }

} else {
    $_SESSION['message'] = [
        'text' => 'Veuillez remplir tous les champs obligatoires',
        'type' => 'danger'
    ];
}

header('Location: ../vue/categorie.php');
exit;
