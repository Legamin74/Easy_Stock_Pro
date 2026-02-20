<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

// Récupération des filtres
$filtre_date = $_GET['filtre_date'] ?? '';
$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';
$filtre_client = $_GET['filtre_client'] ?? '';
$filtre_vendeur = $_GET['filtre_vendeur'] ?? '';
$filtre_statut = $_GET['filtre_statut'] ?? '';

// Construction des filtres
$filtres = [];
if (!empty($filtre_date)) {
    $filtres['filtre_date'] = $filtre_date;
}
if (!empty($date_debut)) {
    $filtres['date_debut'] = $date_debut;
}
if (!empty($date_fin)) {
    $filtres['date_fin'] = $date_fin;
}
if (!empty($filtre_client)) {
    $filtres['client'] = $filtre_client;
}
if (!empty($filtre_vendeur)) {
    $filtres['vendeur'] = $filtre_vendeur;
}
if (!empty($filtre_statut)) {
    $filtres['statut'] = $filtre_statut;
}

// Récupération des ventes avec filtres
if (!empty($filtres)) {
    $ventes = filtrerVentes($filtres);
} else {
    $ventes = getAllVente();
}

$clients = getClientActif();
$vendeurs = getUtilisateursActifs();
$devise = getConfig('devise', 'FCFA');
?>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.page-header h1 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    color: #1a2b3c;
    margin: 0;
}

/* ========== FILTRES ========== */
.filtres-section {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #f0f0f0;
}

.filtres-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    cursor: pointer;
}

.filtres-header h3 {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 16px;
    color: #1a2b3c;
    margin: 0;
}

.filtres-header i {
    transition: transform 0.3s;
}

.filtres-header.collapsed i {
    transform: rotate(-90deg);
}

.filtres-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    transition: all 0.3s;
}

.filtres-content.collapsed {
    display: none;
}

.filtre-groupe {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filtre-groupe label {
    font-size: 13px;
    font-weight: 600;
    color: #5f6b7a;
    text-transform: uppercase;
}

.filtre-groupe select,
.filtre-groupe input {
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: 0.3s;
}

.filtre-groupe select:focus,
.filtre-groupe input:focus {
    border-color: #0b5e2e;
    outline: none;
}

.filtres-actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 15px;
    margin-top: 10px;
}

.btn-filtrer {
    background: #0b5e2e;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
}

.btn-filtrer:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.btn-reset {
    background: #f5f5f5;
    color: #666;
    border: 1px solid #ddd;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: 0.3s;
}

.btn-reset:hover {
    background: #e8e8e8;
}

.btn-export {
    background: #4caf50;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    white-space: nowrap;
}

.btn-export:hover {
    background: #388e3c;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(76, 175, 80, 0.3);
}

.filtre-date-options {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 5px;
}

.filtre-date-options label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: normal;
    text-transform: none;
    font-size: 13px;
    cursor: pointer;
}

.filtre-personnalise {
    grid-column: 1 / -1;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px dashed #ddd;
}

.alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-weight: 500;
}

.alert.success {
    background: #e3f5e9;
    color: #0b5e2e;
    border-left: 4px solid #0b5e2e;
}

.alert.danger {
    background: #fee9e7;
    color: #b3261e;
    border-left: 4px solid #b3261e;
}

.table-responsive {
    overflow-x: auto;
    background: white;
    border-radius: 12px;
    padding: 5px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.mtable {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.mtable th {
    background: #0b5e2e;
    color: white;
    padding: 15px 12px;
    text-align: left;
    font-weight: 500;
}

.mtable td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.mtable tbody tr:hover {
    background: #f8f9fa;
}

.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.badge.success {
    background: rgba(43, 212, 125, 0.1);
    color: #2bd47d;
}

.badge.warning {
    background: rgba(255, 167, 38, 0.1);
    color: #ffa726;
}

.actions {
    display: flex;
    gap: 8px;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #0b5e2e;
    color: white;
    text-decoration: none;
    transition: 0.3s;
}

.btn-action:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.btn-action.danger {
    background: #f44336;
}

.btn-action.danger:hover {
    background: #d32f2f;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

.empty-message {
    text-align: center;
    padding: 40px !important;
    color: #8f9baf;
}

.empty-message i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
    color: #ccc;
}

.filtres-actifs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.filtre-tag {
    background: #e3f2fd;
    color: #1565c0;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filtre-tag a {
    color: #1565c0;
    text-decoration: none;
    font-weight: bold;
}

.filtre-tag a:hover {
    text-decoration: underline;
}

.range-label {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    text-align: center;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .filtres-content {
        grid-template-columns: 1fr;
    }
    
    .filtre-personnalise {
        grid-template-columns: 1fr;
    }
    
    .filtres-actions {
        flex-direction: column;
    }
    
    .btn-export {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="home-content">
    <!-- En-tête -->
    <div class="page-header">
        <h1>
            <i class="bx bx-history"></i> 
            Historique des ventes
        </h1>
        <a href="../model/export/export_ventes.php" class="btn-export" target="_blank">
            <i class="bx bx-download"></i> Export CSV
        </a>
    </div>

    <!-- Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= $_SESSION['message']['type'] ?>">
            <?= $_SESSION['message']['text'] ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Section filtres -->
    <div class="filtres-section">
        <div class="filtres-header" onclick="toggleFiltres()">
            <h3>
                <i class="bx bx-filter-alt"></i>
                Filtrer les ventes
            </h3>
            <i class="bx bx-chevron-down" id="filtresToggle"></i>
        </div>

        <form method="GET" class="filtres-content" id="filtresContent">
            <div class="filtre-groupe">
                <label>Période</label>
                <select name="filtre_date" id="filtre_date" onchange="toggleDatePerso()">
                    <option value="">Toutes les dates</option>
                    <option value="aujourdhui" <?= $filtre_date == 'aujourdhui' ? 'selected' : '' ?>>Aujourd'hui</option>
                    <option value="hier" <?= $filtre_date == 'hier' ? 'selected' : '' ?>>Hier</option>
                    <option value="semaine" <?= $filtre_date == 'semaine' ? 'selected' : '' ?>>Cette semaine</option>
                    <option value="mois" <?= $filtre_date == 'mois' ? 'selected' : '' ?>>Ce mois</option>
                    <option value="personnalise" <?= $filtre_date == 'personnalise' ? 'selected' : '' ?>>Personnalisé</option>
                </select>
                
                <div id="datePersonnalise" style="display: <?= $filtre_date == 'personnalise' ? 'block' : 'none' ?>;">
                    <div class="filtre-personnalise">
                        <div class="filtre-groupe">
                            <label>Date début</label>
                            <input type="date" name="date_debut" value="<?= $date_debut ?>">
                        </div>
                        <div class="filtre-groupe">
                            <label>Date fin</label>
                            <input type="date" name="date_fin" value="<?= $date_fin ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="filtre-groupe">
                <label>Client</label>
                <select name="filtre_client">
                    <option value="">Tous les clients</option>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>" <?= $filtre_client == $client['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($client['prenom'] . ' ' . $client['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filtre-groupe">
                <label>Vendeur</label>
                <select name="filtre_vendeur">
                    <option value="">Tous les vendeurs</option>
                    <?php foreach ($vendeurs as $vendeur): ?>
                        <option value="<?= $vendeur['id'] ?>" <?= $filtre_vendeur == $vendeur['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($vendeur['prenom'] . ' ' . $vendeur['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filtre-groupe">
                <label>Statut</label>
                <select name="filtre_statut">
                    <option value="">Tous</option>
                    <option value="imprime" <?= $filtre_statut == 'imprime' ? 'selected' : '' ?>>Imprimé</option>
                    <option value="non_imprime" <?= $filtre_statut == 'non_imprime' ? 'selected' : '' ?>>Non imprimé</option>
                </select>
            </div>

            <div class="filtres-actions">
                <button type="submit" class="btn-filtrer">
                    <i class="bx bx-filter-alt"></i> Appliquer les filtres
                </button>
                <a href="vente_liste.php" class="btn-reset">
                    <i class="bx bx-reset"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Filtres actifs -->
    <?php 
    $filtres_actifs = [];
    if (!empty($filtre_date)) {
        switch($filtre_date) {
            case 'aujourdhui': $filtres_actifs[] = 'Aujourd\'hui'; break;
            case 'hier': $filtres_actifs[] = 'Hier'; break;
            case 'semaine': $filtres_actifs[] = 'Cette semaine'; break;
            case 'mois': $filtres_actifs[] = 'Ce mois'; break;
            case 'personnalise': 
                if ($date_debut || $date_fin) {
                    $filtres_actifs[] = 'Du ' . $date_debut . ' au ' . $date_fin;
                }
                break;
        }
    }
    if (!empty($filtre_client)) {
        foreach ($clients as $client) {
            if ($client['id'] == $filtre_client) {
                $filtres_actifs[] = 'Client: ' . $client['prenom'] . ' ' . $client['nom'];
                break;
            }
        }
    }
    if (!empty($filtre_vendeur)) {
        foreach ($vendeurs as $vendeur) {
            if ($vendeur['id'] == $filtre_vendeur) {
                $filtres_actifs[] = 'Vendeur: ' . $vendeur['prenom'] . ' ' . $vendeur['nom'];
                break;
            }
        }
    }
    if (!empty($filtre_statut)) {
        $filtres_actifs[] = 'Statut: ' . ($filtre_statut == 'imprime' ? 'Imprimé' : 'Non imprimé');
    }
    ?>

    <?php if (!empty($filtres_actifs)): ?>
    <div class="filtres-actifs">
        <?php foreach ($filtres_actifs as $filtre): ?>
            <span class="filtre-tag">
                <?= $filtre ?>
                <a href="vente_liste.php?<?= http_build_query(array_diff_key($_GET, ['filtre_date'=>1, 'date_debut'=>1, 'date_fin'=>1, 'filtre_client'=>1, 'filtre_vendeur'=>1, 'filtre_statut'=>1])) ?>">✕</a>
            </span>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Tableau des ventes -->
    <div class="table-responsive">
        <table class="mtable">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Nb articles</th>
                    <th>Total</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventes)): ?>
                    <tr>
                        <td colspan="8" class="empty-message">
                            <i class="bx bx-cart"></i>
                            <p>Aucune vente trouvée</p>
                            <?php if (!empty($filtres)): ?>
                                <a href="vente_liste.php" style="color: #0b5e2e;">Voir toutes les ventes</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventes as $v): 
                        $id = $v['id'] ?? 0;
                        $date_vente = $v['date_vente'] ?? '';
                        $client = trim(($v['client_prenom'] ?? '') . ' ' . ($v['client_nom'] ?? '')) ?: 'Client inconnu';
                        $vendeur = trim(($v['vendeur_prenom'] ?? '') . ' ' . ($v['vendeur_nom'] ?? '')) ?: 'Inconnu';
                        $total = $v['total_global'] ?? 0;
                        $imprime = $v['imprime'] ?? 0;

                        $details = getVenteById($id);
                        $nb_articles = count($details['articles'] ?? []);

                        $classe_etat = $imprime ? 'success' : 'warning';
                        $texte_etat = $imprime ? 'Imprimé' : 'Non imprimé';
                    ?>
                    <tr>
                        <td><strong>#<?= $id ?></strong></td>
                        <td><?= $date_vente ? date('d/m/Y H:i', strtotime($date_vente)) : 'Date inconnue' ?></td>
                        <td><?= htmlspecialchars($client) ?></td>
                        <td><?= htmlspecialchars($vendeur) ?></td>
                        <td class="text-center"><?= $nb_articles ?></td>
                        <td class="text-right"><?= number_format($total, 0, ',', ' ') ?> <?= $devise ?></td>
                        <td>
                            <span class="badge <?= $classe_etat ?>"><?= $texte_etat ?></span>
                        </td>
                        <td class="actions">
                            <a href="recu.php?id_vente=<?= $id ?>" target="_blank" class="btn-action" title="Voir reçu">
                                <i class="bx bx-receipt"></i>
                            </a>
                            <?php if (estAdmin() && !$imprime): ?>
                                <a href="../model/annulerVente.php?id=<?= $id ?>" 
                                   class="btn-action danger" 
                                   onclick="return confirm('Annuler cette vente ?')"
                                   title="Annuler">
                                    <i class="bx bx-x-circle"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleFiltres() {
    const content = document.getElementById('filtresContent');
    const toggle = document.getElementById('filtresToggle');
    content.classList.toggle('collapsed');
    toggle.style.transform = content.classList.contains('collapsed') ? 'rotate(-90deg)' : 'rotate(0)';
}

function toggleDatePerso() {
    const select = document.getElementById('filtre_date');
    const perso = document.getElementById('datePersonnalise');
    perso.style.display = select.value === 'personnalise' ? 'block' : 'none';
}

// Initialiser l'état au chargement
document.addEventListener('DOMContentLoaded', function() {
    toggleDatePerso();
    
    // Ouvrir les filtres si des filtres sont actifs
    <?php if (!empty($filtres)): ?>
    const content = document.getElementById('filtresContent');
    content.classList.remove('collapsed');
    <?php endif; ?>
});
</script>

<?php include 'pied.php'; ?>