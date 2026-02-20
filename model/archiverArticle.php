<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $sql = "UPDATE article SET statut = 'archive' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => " Article archivé avec succès",
        'type' => 'success'
    ];
} else {
    $_SESSION['message'] = [
        'text' => " ID article manquant",
        'type' => 'danger'
    ];
}

header('Location: ../vue/article.php');
exit;