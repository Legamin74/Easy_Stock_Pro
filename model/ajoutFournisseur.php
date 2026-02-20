<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'] ?? null;

    $sql = "INSERT INTO fournisseur (nom, prenom, telephone, adresse) VALUES (?, ?, ?, ?)";
    $req = $connexion->prepare($sql);
    $result = $req->execute([$nom, $prenom, $telephone, $adresse]);

    if ($result) {
        $_SESSION['message'] = [
            'text' => ' Fournisseur ajouté avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur lors de l\'ajout',
            'type' => 'danger'
        ];
    }
}


header('Location: ../vue/fournisseur.php');
exit;