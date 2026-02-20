<?php
session_start();
require_once 'connexion.php';

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM categorie_article WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);

    if ($req->rowCount() > 0) {
        $_SESSION['message'] = [
            'text' => "Catégorie supprimée avec succès",
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => "Erreur lors de la suppression ou catégorie introuvable",
            'type' => 'danger'
        ];
    }
} else {
    $_SESSION['message'] = [
        'text' => "ID de catégorie manquant",
        'type' => 'danger'
    ];
}

header('Location: ../vue/categorie.php');
exit;
