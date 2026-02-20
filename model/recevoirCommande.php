<?php
require_once 'connexion.php'; // session_start() déjà fait ici
require_once 'fonction.php';  // Pour avoir la fonction recevoirCommande()

if (!empty($_GET['id'])) {
    $id_commande = (int)$_GET['id'];
    
    // Vérifier que la commande existe
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
            'text' => 'Cette commande a deja ete recue',
            'type' => 'warning'
        ];
        header('Location: ../vue/commande.php');
        exit;
    }
    
    // Appeler la fonction de reception
    if (recevoirCommande($id_commande)) {
        $_SESSION['message'] = [
            'text' => 'Commande recue avec succes. Stock mis a jour.',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => 'Erreur lors de la reception',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/commande.php');
exit;