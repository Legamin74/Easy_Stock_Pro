<?php
session_start();
include 'connexion.php';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    !empty($_POST['nom']) &&
    !empty($_POST['prenom']) &&
    !empty($_POST['telephone']) &&
    !empty($_POST['adresse'])
) {

    //  Vérifier si le client existe déjà (par téléphone)
    $checkSql = "SELECT id FROM client WHERE telephone = ?";
    $checkReq = $connexion->prepare($checkSql);
    $checkReq->execute([$_POST['telephone']]);

    if ($checkReq->rowCount() > 0) {
        // Client déjà existant
        $_SESSION['message']['text'] = "Ce client existe déjà (numéro de téléphone déjà utilisé)";
        $_SESSION['message']['type'] = "warning";
    } else {

        //  Insertion du client
        $sql = "INSERT INTO client (nom, prenom, telephone, adresse)
                VALUES (?, ?, ?, ?)";

        $req = $connexion->prepare($sql);
        $req->execute([
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['telephone'],
            $_POST['adresse']
        ]);

        if ($req->rowCount() > 0) {
            $_SESSION['message']['text'] = "Client ajouté avec succès";
            $_SESSION['message']['type'] = "success";
        } else {
            $_SESSION['message']['text'] = "Une erreur s'est produite lors de l'ajout du client";
            $_SESSION['message']['type'] = "danger";
        }
    }

} else {
    $_SESSION['message']['text'] = "Une information obligatoire non renseignée";
    $_SESSION['message']['type'] = "danger";
}

header('Location: ../vue/client.php');
exit;
