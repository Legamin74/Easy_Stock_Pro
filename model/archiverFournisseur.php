<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $sql = "UPDATE fournisseur SET statut = 'archive' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => " Fournisseur archivé avec succès",
        'type' => 'success'
    ];
} else {
    $_SESSION['message'] = [
        'text' => " ID fournisseur manquant",
        'type' => 'danger'
    ];
}

header('Location: ../vue/fournisseur.php');
exit;