<?php
//  NE PAS refaire session_start() car déjà fait dans connexion.php
require_once 'connexion.php';
require_once 'fonction.php'; //  AJOUTE CECI POUR CHARGER LES FONCTIONS

// Vérifier que l'utilisateur est admin
if (!estConnecte() || !estAdmin()) {
    $_SESSION['message'] = [
        'text' => ' Vous n\'avez pas les droits pour annuler une vente',
        'type' => 'danger'
    ];
    header('Location: ../vue/ventes_liste.php');
    exit;
}

if (empty($_GET['id'])) {
    $_SESSION['message'] = [
        'text' => ' ID vente manquant',
        'type' => 'danger'
    ];
    header('Location: ../vue/ventes_liste.php');
    exit;
}

$id_vente = (int)$_GET['id'];

// Vérifier si la vente existe et n'est pas déjà imprimée
$sql = "SELECT imprime FROM vente WHERE id = ? AND etat = '1'";
$req = $connexion->prepare($sql);
$req->execute([$id_vente]);
$vente = $req->fetch(PDO::FETCH_ASSOC);

if (!$vente) {
    $_SESSION['message'] = [
        'text' => ' Vente introuvable',
        'type' => 'danger'
    ];
    header('Location: ../vue/ventes_liste.php');
    exit;
}

if ($vente['imprime'] == 1) {
    $_SESSION['message'] = [
        'text' => ' Impossible d\'annuler une vente déjà imprimée',
        'type' => 'danger'
    ];
    header('Location: ../vue/ventes_liste.php');
    exit;
}

//  Appeler la fonction d'annulation
if (annulerVente($id_vente)) {
    $_SESSION['message'] = [
        'text' => ' Vente annulée avec succès',
        'type' => 'success'
    ];
} else {
    $_SESSION['message'] = [
        'text' => ' Erreur lors de l\'annulation',
        'type' => 'danger'
    ];
}

header('Location: ../vue/vente_liste.php');
exit;