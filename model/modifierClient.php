<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $telephone = $_POST['telephone'];
    $adresse = $_POST['adresse'] ?? null;

    $sql = "UPDATE client SET nom = ?, prenom = ?, telephone = ?, adresse = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    $result = $req->execute([$nom, $prenom, $telephone, $adresse, $id]);

    if ($result) {
        $_SESSION['message'] = [
            'text' => ' Client modifié avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur lors de la modification',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/client.php');
exit;