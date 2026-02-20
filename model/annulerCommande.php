<?php
include 'connexion.php';
session_start();

if (!empty($_GET['id'])) {
    $id_commande = (int)$_GET['id'];
    
    // Vérifier que la commande existe et peut être annulée
    $sql = "SELECT statut FROM commande WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_commande]);
    $commande = $req->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande) {
        $_SESSION['message'] = [
            'text' => 'Commande introuvable',
            'type' => 'danger'
        ];
        header('Location: ../vue/commande.php');
        exit;
    }
    
    if ($commande['statut'] == 'livree') {
        $_SESSION['message'] = [
            'text' => 'Impossible d\'annuler une commande déjà livrée',
            'type' => 'danger'
        ];
        header('Location: ../vue/commande.php');
        exit;
    }
    
    // Annuler la commande
    if (annulerCommande($id_commande)) {
        $_SESSION['message'] = [
            'text' => 'Commande annulée avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => 'Erreur lors de l\'annulation',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/commande.php');
exit;