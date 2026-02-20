<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $id_commande = (int)$_GET['id'];
    
    // Pour les commandes, "archiver" peut signifier :
    // - Les marquer comme annulées (si pas encore traitées)
    // - Ou les masquer de la liste principale
    
    $sql = "UPDATE commande SET statut = 'annulee' WHERE id = ?";
    $req = $connexion->prepare($sql);
    $result = $req->execute([$id_commande]);
    
    if ($result) {
        $_SESSION['message'] = [
            'text' => 'Commande archivée avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => 'Erreur lors de l\'archivage',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/commande.php');
exit;