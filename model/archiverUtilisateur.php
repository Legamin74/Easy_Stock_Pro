<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id']) && $_GET['id'] != $_SESSION['user']['id']) {
    $sql = "UPDATE utilisateur SET statut = 'archive' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => ' Utilisateur archivé avec succès',
        'type' => 'success'
    ];
} else {
    $_SESSION['message'] = [
        'text' => ' Impossible d\'archiver cet utilisateur',
        'type' => 'danger'
    ];
}

header('Location: ../vue/utilisateur.php');
exit;