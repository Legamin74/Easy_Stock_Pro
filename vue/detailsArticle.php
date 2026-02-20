<?php
if(session_status() === PHP_SESSION_NONE) session_start();

include '../model/connexion.php';

if (empty($_GET['id'])) {
    header('Location: article.php');
    exit;
}

// Récupérer l'article
$sql = "SELECT * FROM article WHERE id = ?";
$req = $connexion->prepare($sql);
$req->execute([$_GET['id']]);
$article = $req->fetch(PDO::FETCH_ASSOC);

if (!$article) {
    $_SESSION['message']['text'] = "Article introuvable";
    $_SESSION['message']['type'] = "danger";
    header('Location: article.php');
    exit;
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Détails de l'article</h4>
        </div>
        <div class="card-body">
            <ul class="list-group mb-3">
                 <li class="list-group-item"><strong>Nom :</strong> <?= ($article['nom_article']) ?></li>
                <li class="list-group-item"><strong>Catégorie :</strong> <?= htmlspecialchars($article['id_categorie']) ?></li>
                <li class="list-group-item"><strong>Quantité :</strong> <?= htmlspecialchars($article['quantite']) ?></li>
                <li class="list-group-item"><strong>Prix unitaire :</strong> <?= htmlspecialchars($article['prix_unitaire']) ?></li>
                <li class="list-group-item"><strong>Date d'enregistrement :</strong> <?= htmlspecialchars($article['date_fabrication']) ?></li>
                <li class="list-group-item"><strong>Date d'expiration :</strong> <?= htmlspecialchars($article['date_expiration']) ?></li>
            </ul>
            <a href="article.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>

<?php include 'pied.php'; ?>
