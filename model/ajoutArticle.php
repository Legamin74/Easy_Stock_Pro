<?php
require_once 'connexion.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données
    $nom = $_POST['nom_article'];
    $id_categorie = !empty($_POST['id_categorie']) ? $_POST['id_categorie'] : null;
    $quantite = (int)$_POST['quantite'];
    $prix = (int)$_POST['prix_unitaire'];
    $seuil = (int)($_POST['seuil_alerte'] ?? 5);
    
    // Gestion de la date d'expiration
    $date_expiration = null;
    if (!empty($_POST['date_expiration'])) {
        $date_expiration = $_POST['date_expiration'];
    }
     $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($extension, $extensions)) {
            if ($_FILES['image']['size'] <= 2 * 1024 * 1024) { // 2 Mo max
                $nom_fichier = 'article_' . date('Ymd_His') . '.' . $extension;
                $chemin_destination = __DIR__ . '/../public/uploads/articles/' . $nom_fichier;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $chemin_destination)) {
                    $image = 'uploads/articles/' . $nom_fichier;
                }
            }
        }
    }
 

    // Requête SQL
   $sql = "INSERT INTO article (nom_article, id_categorie, quantite, prix_unitaire, seuil_alerte, date_expiration, image) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $req = $connexion->prepare($sql);
    
    if ($req) {
        $result = $req->execute([$nom, $id_categorie, $quantite, $prix, $seuil, $date_expiration,$image]);

        if ($result) {
            $_SESSION['message'] = [
                'text' => ' Article ajouté avec succès',
                'type' => 'success'
            ];
        } else {
            $_SESSION['message'] = [
                'text' => ' Erreur lors de l\'ajout',
                'type' => 'danger'
            ];
        }

    } else {
        $_SESSION['message'] = [
            'text' => ' Erreur de préparation de la requête',
            'type' => 'danger'
        ];
    }
}

header('Location: ../vue/article.php');
exit;