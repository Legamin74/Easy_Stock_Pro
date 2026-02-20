<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $sql = "UPDATE client SET statut = 'archive' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$_GET['id']]);

    $_SESSION['message'] = [
        'text' => " Client archivé avec succès",
        'type' => 'success'
    ];
} else {
    $_SESSION['message'] = [
        'text' => " ID client manquant",
        'type' => 'danger'
    ];
}

header('Location: ../vue/client.php');
exit;