<?php
require_once '../fonction.php';
require_once '../connexion.php';
// Vérifier les droits (tous les connectés peuvent exporter)
if (!estConnecte()) {
    die('Accès non autorisé');
}

// Récupérer toutes les ventes actives
$ventes = getAllVente();

// Préparer les données pour l'export
$data = [];
foreach ($ventes as $v) {
    // Récupérer les détails de la vente pour avoir les articles
    $details = getVenteById($v['id']);
    $articles = $details['articles'] ?? [];
    
    // Construire une ligne par vente
    $liste_articles = [];
    foreach ($articles as $a) {
        $liste_articles[] = $a['nom_article'] . ' (x' . $a['quantite'] . ')';
    }
    
    $data[] = [
        'ID Vente' => $v['id'],
        'Date' => date('d/m/Y H:i', strtotime($v['date_vente'])),
        'Client' => $v['client_prenom'] . ' ' . $v['client_nom'],
        'Vendeur' => trim(($v['vendeur_prenom'] ?? '') . ' ' . ($v['vendeur_nom'] ?? 'Admin')),
        'Articles' => implode(', ', $liste_articles),
        'Total' => $v['total_global'],
        'Devise' => getConfig('devise', 'FCFA'),
        'État' => $v['imprime'] ? 'Imprimé' : 'Non imprimé'
    ];
}

// Définir les colonnes
$colonnes = ['ID Vente', 'Date', 'Client', 'Vendeur', 'Articles', 'Total', 'Devise', 'État'];

// Appeler la fonction d'export CSV
exportCSV($data, 'historique_ventes', $colonnes);

/**
 * Fonction d'export CSV (à copier dans fonction.php ou garder ici)
 */
function exportCSV($data, $filename, $colonnes = []) {
    $filename = $filename . '_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM pour Excel
    
    if (!empty($colonnes)) {
        fputcsv($output, $colonnes, ';');
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
    exit;
}
?>