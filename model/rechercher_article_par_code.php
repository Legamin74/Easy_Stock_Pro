<?php
require_once 'connexion.php';
require_once 'fonction.php';



if (!empty($_POST['code'])) {
    $code = $_POST['code'];
    
    $sql = "SELECT * FROM article WHERE code_barre = ? AND statut = 'actif'";
    $req = $connexion->prepare($sql);
    $req->execute([$code]);
    $article = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($article) {
        echo json_encode([
            'success' => true,
            'article' => [
                'id' => $article['id'],
                'nom' => $article['nom_article'],
                'prix' => $article['prix_unitaire'],
                'image' => $article['image'] ?? null
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Article non trouvé']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Code vide']);
}