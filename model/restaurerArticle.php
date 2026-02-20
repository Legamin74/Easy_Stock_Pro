<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $sql = "UPDATE article SET statut = 'actif' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => " Article restauré avec succès",
        'type' => 'success'
    ];
}

header('Location: ../vue/article_archive.php');
exit;