<?php
require_once 'connexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nom = $_POST['nom_article'];
    $id_categorie = !empty($_POST['id_categorie']) ? $_POST['id_categorie'] : null;
    $quantite = (int)$_POST['quantite'];
    $prix = (int)$_POST['prix_unitaire'];
    $seuil = (int)($_POST['seuil_alerte'] ?? 5);
    $image_actuelle = $_POST['image_actuelle'] ?? null;
    
    // Gestion de la date d'expiration
    $date_expiration = null;
    if (!empty($_POST['date_expiration'])) {
        $date_expiration = $_POST['date_expiration'];
    }
    
    
    $image = $image_actuelle; 
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, $extensions) && $_FILES['image']['size'] <= 2 * 1024 * 1024) {
            
            $nom_fichier = 'article_' . date('Ymd_His') . '.' . $extension;
            $chemin_destination = __DIR__ . '/../public/uploads/articles/' . $nom_fichier;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin_destination)) {
                
                if (!empty($image_actuelle) && file_exists(__DIR__ . '/../public/' . $image_actuelle)) {
                    unlink(__DIR__ . '/../public/' . $image_actuelle);
                }
                $image = 'uploads/articles/' . $nom_fichier;
            }
        }
    }
    
  
    $sql = "UPDATE article SET 
                nom_article = ?, 
                id_categorie = ?, 
                quantite = ?, 
                prix_unitaire = ?, 
                seuil_alerte = ?, 
                date_expiration = ?,
                image = ?
            WHERE id = ?";
    
    $req = $connexion->prepare($sql);
    $result = $req->execute([$nom, $id_categorie, $quantite, $prix, $seuil, $date_expiration, $image, $id]);
    
    if ($result) {
        $_SESSION['message'] = [
            'text' => ' Article modifié avec succès',
            'type' => 'success'
        ];
    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur lors de la modification',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/article.php');
exit;