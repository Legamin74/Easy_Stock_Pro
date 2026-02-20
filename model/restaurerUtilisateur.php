<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $sql = "UPDATE utilisateur SET statut = 'actif' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => ' Utilisateur restauré avec succès',
        'type' => 'success'
    ];
}

header('Location: ../vue/utilisateur_archive.php');
exit;