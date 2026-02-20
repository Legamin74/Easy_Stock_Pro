<?php
session_start();
include 'connexion.php';

if (!empty($_GET['id'])) {

    //  Vérifier si l'article existe
    $checkSql = "SELECT id FROM article WHERE id = ?";
    $checkReq = $connexion->prepare($checkSql);
    $checkReq->execute([$_GET['id']]);

    if ($checkReq->rowCount() > 0) {

        //  Suppression de l'article
        $sql = "DELETE FROM article WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$_GET['id']]);

        if ($req->rowCount() > 0) {
            $_SESSION['message']['text'] = "Article supprimé avec succès";
            $_SESSION['message']['type'] = "success";
        } else {
            $_SESSION['message']['text'] = "Erreur lors de la suppression de l'article";
            $_SESSION['message']['type'] = "danger";
        }

    } else {
        $_SESSION['message']['text'] = "Article introuvable";
        $_SESSION['message']['type'] = "warning";
    }

} else {
    $_SESSION['message']['text'] = "Identifiant de l'article manquant";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/article.php');
exit;
