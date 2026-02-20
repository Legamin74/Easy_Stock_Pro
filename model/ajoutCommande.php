<?php
include 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_fournisseur = $_POST['id_fournisseur'];
    $date_livraison = !empty($_POST['date_livraison']) ? $_POST['date_livraison'] : null;
    $notes = $_POST['notes'] ?? '';
    
    // Récupérer le panier depuis la session
    if (!isset($_SESSION['panier_commande']) || empty($_SESSION['panier_commande'])) {
        $_SESSION['message'] = ['text' => 'Panier vide', 'type' => 'danger'];
        header('Location: ../vue/commande_ajout.php');
        exit;
    }
    
    $panier = $_SESSION['panier_commande'];
    
    // Préparer les articles
    $articles = [];
    foreach ($panier as $item) {
        $articles[] = [
            'id_article' => $item['id_article'],
            'quantite' => $item['quantite'],
            'prix_unitaire' => $item['prix']
        ];
    }
    
    // Créer la commande
    $id_commande = creerCommande($id_fournisseur, $articles, $date_livraison, $notes);
    
    if ($id_commande) {
        $_SESSION['panier_commande'] = [];
        $_SESSION['message'] = [
            'text' => 'Commande créée avec succès',
            'type' => 'success'
        ];
        header('Location: ../vue/commande_details.php?id=' . $id_commande);
    } else {
        $_SESSION['message'] = [
            'text' => 'Erreur lors de la création de la commande',
            'type' => 'danger'
        ];
        header('Location: ../vue/commande_ajout.php');
    }
    exit;
}

header('Location: ../vue/commande.php');
exit;