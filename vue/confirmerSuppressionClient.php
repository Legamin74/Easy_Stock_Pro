<?php
include '../model/connexion.php';

if (empty($_GET['id'])) {
    header('Location: client.php');
    exit;
}

// Récupération du client
$sql = "SELECT * FROM client WHERE id = ?";
$req = $connexion->prepare($sql);
$req->execute([$_GET['id']]);
$client = $req->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    $_SESSION['message']['text'] = "Client introuvable";
    $_SESSION['message']['type'] = "danger";
    header('Location: client.php');
    exit;
}
?>

<h2>Confirmation de suppression</h2>

<p>Voulez-vous vraiment supprimer le client suivant ?</p>

<ul>
    <li><strong>Nom :</strong> <?= htmlspecialchars($client['nom']) ?></li>
    <li><strong>Prénom :</strong> <?= htmlspecialchars($client['prenom']) ?></li>
    <li><strong>Téléphone :</strong> <?= htmlspecialchars($client['telephone']) ?></li>
    <li><strong>Adresse :</strong> <?= htmlspecialchars($client['adresse']) ?></li>
</ul>

<form action="../model/supprimerClient.php" method="POST">
    <input type="hidden" name="id" value="<?= $client['id'] ?>">

    <button type="submit" class="btn-danger">
        Supprimer
    </button>

    <a href="client.php" class="btn-cancel">
        Annuler
    </a>
</form>
