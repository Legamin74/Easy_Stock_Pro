<?php

include '../model/connexion.php';

if (empty($_GET['id'])) {
    header('Location: client.php');
    exit;
}

// Récupérer le client
$sql = "SELECT * FROM client WHERE id = ?";
$req = $connexion->prepare($sql);
$req->execute([$_GET['id']]);
$client = $req->fetch(PDO::FETCH_ASSOC);

/*if (!$client) {
    $_SESSION['message']['text'] = "Client introuvable";
    $_SESSION['message']['type'] = "danger";
    header('Location: client.php');
    exit;
}*/
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4>Détails du client</h4>
        </div>
        <div class="card-body">
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Nom :</strong> <?= htmlspecialchars($client['nom']) ?></li>
                <li class="list-group-item"><strong>Prénom :</strong> <?= htmlspecialchars($client['prenom']) ?></li>
                <li class="list-group-item"><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></li>
                <li class="list-group-item"><strong>Adresse :</strong> <?= htmlspecialchars($client['adresse']) ?></li>
            </ul>
            <a href="client.php" class="btn btn-secondary">Retour</a>
        </div>
    </div>
</div>


