<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$nom_serveur = "localhost";
$nom_base_de_donne = "gestion_stock_dclic";
$utilisateur = "root";
$mot_de_passe = "1234";

try {
    $connexion = new PDO(
        "mysql:host=$nom_serveur;dbname=$nom_base_de_donne;charset=utf8",
        $utilisateur,
        $mot_de_passe
    );

    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>
