<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commande = $_POST['id_commande'];
    $id_fournisseur = $_POST['id_fournisseur'];
    $date_livraison = !empty($_POST['date_livraison']) ? $_POST['date_livraison'] : null;
    $notes = $_POST['notes'] ?? '';
    
    // Vérifier que la commande existe et est modifiable (en_attente)
    $sql = "SELECT statut FROM commande WHERE id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id_commande]);
    $commande = $req->fetch(PDO::FETCH_ASSOC);
    
    if (!$commande || $commande['statut'] != 'en_attente') {
        $_SESSION['message'] = [
            'text' => 'Cette commande ne peut pas être modifiée',
            'type' => 'danger'
        ];
        header('Location: ../vue/commande.php');
        exit;
    }
    
    // Mettre à jour la commande
    $sql = "UPDATE commande SET 
                id_fournisseur = ?,
                date_livraison_prevue = ?,
                notes = ?
            WHERE id = ?";
    $req = $connexion->prepare($sql);
    $result = $req->execute([$id_fournisseur, $date_livraison, $notes, $id_commande]);
    
    if ($result) {
        $_SESSION['message'] = [
            'text' => 'Commande modifiée avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => 'Erreur lors de la modification',
            'type' => 'danger'
        ];
    }
    
    header('Location: ../vue/commande_details.php?id=' . $id_commande);
    exit;
}

header('Location: ../vue/commande.php');
exit;