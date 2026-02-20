<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

// Initialiser le panier en session
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Ajouter un article au panier
if (isset($_POST['ajouter_panier'])) {
    $id_article = $_POST['id_article'];
    $quantite = (int)$_POST['quantite'];
    
    $sql = "SELECT * FROM article WHERE id = ? AND statut = 'actif'";
    $req = $connexion->prepare($sql);
    $req->execute([$id_article]);
    $article = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($article && $article['quantite'] >= $quantite) {
        $trouve = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['id_article'] == $id_article) {
                $item['quantite'] += $quantite;
                $trouve = true;
                break;
            }
        }
        
        if (!$trouve) {
            $_SESSION['panier'][] = [
                'id_article' => $article['id'],
                'nom_article' => $article['nom_article'],
                'prix' => $article['prix_unitaire'],
                'quantite' => $quantite
            ];
        }
        
        $_SESSION['message'] = ['text' => 'Article ajoute au panier', 'type' => 'success'];
    } else {
        $_SESSION['message'] = ['text' => 'Stock insuffisant', 'type' => 'danger'];
    }
    
    ?>
    <script>window.location.href = 'vente.php';</script>
    <?php
    exit;
}

// Supprimer un article du panier
if (isset($_GET['supprimer'])) {
    $index = (int)$_GET['supprimer'];
    if (isset($_SESSION['panier'][$index])) {
        unset($_SESSION['panier'][$index]);
        $_SESSION['panier'] = array_values($_SESSION['panier']);
    }
    ?>
    <script>window.location.href = 'vente.php';</script>
    <?php
    exit;
}

// Vider le panier
if (isset($_GET['vider'])) {
    $_SESSION['panier'] = [];
    ?>
    <script>window.location.href = 'vente.php';</script>
    <?php
    exit;
}

// Valider la vente
if (isset($_POST['valider_vente'])) {
    $id_client = (int)$_POST['id_client'];
    $panier = $_SESSION['panier'];
    
    if (empty($panier)) {
        $_SESSION['message'] = ['text' => 'Panier vide', 'type' => 'danger'];
    } elseif (!$id_client) {
        $_SESSION['message'] = ['text' => 'Veuillez selectionner un client', 'type' => 'danger'];
    } else {
        $id_vente = enregistrerVente($id_client, $panier);
        
        if ($id_vente) {
            $_SESSION['panier'] = [];
            $_SESSION['message'] = ['text' => 'Vente enregistree', 'type' => 'success'];
            echo '<script>window.open("recu.php?id_vente=' . $id_vente . '", "_blank");</script>';
        } else {
            $_SESSION['message'] = ['text' => 'Erreur lors de l\'enregistrement', 'type' => 'danger'];
        }
    }
    
    ?>
    <script>window.location.href = 'vente.php';</script>
    <?php
    exit;
}

$panier = $_SESSION['panier'];
$id_client = $_SESSION['id_client'] ?? 0;

// Recuperer le client en session si existe
if (isset($_POST['set_client'])) {
    $_SESSION['id_client'] = (int)$_POST['set_client'];
    exit;
}

// Calculer le total du panier
$total_panier = 0;
foreach ($panier as $item) {
    $total_panier += $item['prix'] * $item['quantite'];
}

// Recuperer les ventes du jour
$ventes_aujourdhui = getVentesDuJour();
?>

<style>
.vente-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

/* Layout 2 colonnes */
.vente-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.vente-col {
    min-width: 0;
}

.vente-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
    height: fit-content;
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f5f5f5;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a2b3c;
    margin: 0;
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

/* Formulaire nouvelle vente */
.client-row {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 20px;
}

.client-select {
    flex: 1;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.3s;
    background: white;
}

.client-select:focus {
    border-color: #0b5e2e;
    outline: none;
    box-shadow: 0 0 0 3px rgba(11, 94, 46, 0.1);
}

.btn-add-client {
    background: #f5f5f5;
    color: #1a2b3c;
    padding: 12px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: 0.3s;
    white-space: nowrap;
    border: 1px solid #e0e0e0;
}

.btn-add-client:hover {
    background: #e8e8e8;
}

.article-row {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #5f6b7a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group select,
.form-group input {
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s;
    background: white;
    width: 100%;
}

.form-group select:focus,
.form-group input:focus {
    border-color: #0b5e2e;
    outline: none;
}

.form-group input[readonly] {
    background: #f8f9fa;
    border-color: #e0e0e0;
    color: #1a2b3c;
}

.prix-ligne {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 5px;
}

.btn-add {
    width: 100%;
    background: #0b5e2e;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 15px;
    cursor: pointer;
    transition: 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
}

.btn-add:hover {
    background: #0a4f26;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(11, 94, 46, 0.2);
}

/* Panier */
.panier-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.btn-clear {
    background: #fee9e7;
    color: #b3261e;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
    transition: 0.3s;
}

.btn-clear:hover {
    background: #fcd9d5;
}

.panier-vide {
    text-align: center;
    padding: 40px;
    color: #8f9baf;
    background: #f8f9fa;
    border-radius: 12px;
    font-size: 15px;
}

.panier-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.panier-table th {
    background: #f8f9fa;
    color: #5f6b7a;
    font-weight: 600;
    font-size: 13px;
    padding: 12px;
    text-align: left;
    border-bottom: 2px solid #e0e0e0;
}

.panier-table td {
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
}

.panier-table tfoot td {
    background: #f8f9fa;
    font-weight: 600;
    color: #1a2b3c;
    border-top: 2px solid #e0e0e0;
    padding: 15px 12px;
}

.btn-remove {
    color: #b3261e;
    text-decoration: none;
    font-size: 18px;
    opacity: 0.7;
}

.btn-remove:hover {
    opacity: 1;
}

.btn-validate {
    width: 100%;
    background: #0b5e2e;
    color: white;
    border: none;
    padding: 16px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    margin-top: 20px;
}

.btn-validate:hover:not(:disabled) {
    background: #0a4f26;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(11, 94, 46, 0.3);
}

.btn-validate:disabled {
    background: #e0e0e0;
    color: #8f9baf;
    cursor: not-allowed;
}

/* Ventes du jour */
.ventes-jour-section {
    margin-top: 30px;
}

.section-title {
    font-size: 20px;
    font-weight: 600;
    color: #1a2b3c;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #0b5e2e;
}

.ventes-jour-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.ventes-jour-table th {
    background: #0b5e2e;
    color: white;
    padding: 12px;
    text-align: left;
}

.ventes-jour-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
}

.ventes-jour-table tr:hover {
    background: #f5f5f5;
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

.btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: #0b5e2e;
    color: white;
    text-decoration: none;
}

.text-right {
    text-align: right;
}

.text-center {
    text-align: center;
}

/* Responsive */
@media (max-width: 992px) {
    .vente-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 700px) {
    .client-row {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-add-client {
        width: 100%;
        justify-content: center;
    }
    
    .prix-ligne {
        grid-template-columns: 1fr;
    }
    
    .panier-table {
        min-width: 600px;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
}
</style>

<div class="home-content">
    <div class="vente-container">
        
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?= $_SESSION['message']['type'] ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- Layout 2 colonnes -->
        <div class="vente-row">
            <!-- Colonne gauche : Nouvelle vente -->
            <div class="vente-col">
                <div class="vente-card">
                    <div class="card-header">
                        <h3>Nouvelle vente</h3>
                    </div>
                    
                    <form method="POST">
                        <!-- Client -->
                        <div class="client-row">
                            <select name="id_client" id="id_client" class="client-select" required>
                                <option value="">-- Choisir un client --</option>
                                <?php
                                $clients = getClientActif();
                                foreach ($clients as $client) {
                                    $selected = ($id_client == $client['id']) ? 'selected' : '';
                                    echo '<option value="' . $client['id'] . '" ' . $selected . '>';
                                    echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']);
                                    echo '</option>';
                                }
                                ?>
                            </select>
                            
                            <a href="client.php" class="btn-add-client" target="_blank">
                                + Nouveau
                            </a>
                        </div>

                        <!-- Article -->
                        <div class="article-row">
                            <div class="form-group">
                                <label>ARTICLE</label>
                                <select name="id_article" id="id_article" required>
                                    <option value="">-- Choisir --</option>
                                    <?php
                                    $articles = getArticleActif();
                                    foreach ($articles as $article):
                                        $stock = $article['quantite'];
                                    ?>
                                    <option value="<?= $article['id'] ?>" 
                                            data-prix="<?= $article['prix_unitaire'] ?>"
                                            data-stock="<?= $stock ?>"
                                            <?= $stock <= 0 ? 'disabled' : '' ?>>
                                        <?= htmlspecialchars($article['nom_article']) ?> 
                                        (<?= $stock ?> dispo)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>QUANTITE</label>
                                <input type="number" name="quantite" id="quantite" min="1" value="1" required>
                            </div>
                            
                            <div class="prix-ligne">
                                <div class="form-group">
                                    <label>PRIX UNITAIRE</label>
                                    <input type="text" id="prix_unitaire" readonly value="0 FCFA">
                                </div>
                                
                                <div class="form-group">
                                    <label>TOTAL</label>
                                    <input type="text" id="total_ligne" readonly value="0 FCFA">
                                </div>
                            </div>
                            
                            <button type="submit" name="ajouter_panier" class="btn-add">
                                + Ajouter au panier
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Colonne droite : Panier -->
            <div class="vente-col">
                <div class="vente-card">
                    <div class="card-header">
                        <h3>Panier</h3>
                        <?php if (!empty($panier)): ?>
                        <a href="vente.php?vider=1" class="btn-clear" onclick="return confirm('Vider le panier ?')">
                            Vider
                        </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($panier)): ?>
                        <div class="panier-vide">
                            Votre panier est vide
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="panier-table">
                                <thead>
                                    <tr>
                                        <th>Article</th>
                                        <th class="text-right">Prix unitaire</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-right">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($panier as $index => $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['nom_article']) ?></td>
                                        <td class="text-right"><?= number_format($item['prix'], 0, ',', ' ') ?> F</td>
                                        <td class="text-center"><?= $item['quantite'] ?></td>
                                        <td class="text-right"><?= number_format($item['prix'] * $item['quantite'], 0, ',', ' ') ?> F</td>
                                        <td class="text-center">
                                            <a href="vente.php?supprimer=<?= $index ?>" class="btn-remove" onclick="return confirm('Retirer ?')">
                                                ✕
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right"><strong>Total general</strong></td>
                                        <td class="text-right"><strong><?= number_format($total_panier, 0, ',', ' ') ?> F</strong></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <form method="POST">
                            <input type="hidden" name="id_client" value="<?= $id_client ?>">
                            <button type="submit" name="valider_vente" class="btn-validate" <?= empty($id_client) ? 'disabled' : '' ?>>
                                Valider la vente
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Section Ventes du jour (en bas) -->
        <div class="ventes-jour-section">
            <h2 class="section-title">Ventes du jour</h2>
            
            <div class="vente-card">
                <div class="table-responsive">
                    <table class="ventes-jour-table">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Client</th>
                                <th>Articles</th>
                                <th>Total</th>
                                <th>Etat</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ventes_aujourdhui)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">
                                        Aucune vente aujourd'hui
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($ventes_aujourdhui as $v): 
                                    $details = getVenteById($v['id']);
                                    $articles = $details['articles'] ?? [];
                                    $liste_articles = [];
                                    foreach ($articles as $a) {
                                        $liste_articles[] = $a['nom_article'] . ' (x' . $a['quantite'] . ')';
                                    }
                                ?>
                                <tr>
                                    <td><?= date('H:i', strtotime($v['date_vente'])) ?></td>
                                    <td><?= htmlspecialchars($v['client_prenom'] . ' ' . $v['client_nom']) ?></td>
                                    <td><?= implode(', ', $liste_articles) ?></td>
                                    <td><?= number_format($v['total_global'], 0, ',', ' ') ?> F</td>
                                    <td>
                                        <span class="badge <?= $v['imprime'] ? 'success' : 'warning' ?>">
                                            <?= $v['imprime'] ? 'Imprime' : 'Non imprime' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="recu.php?id_vente=<?= $v['id'] ?>" target="_blank" class="btn-icon">
                                            <i class="bx bx-receipt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mise a jour des prix
document.getElementById('id_article').addEventListener('change', updatePrix);
document.getElementById('quantite').addEventListener('input', updatePrix);

function updatePrix() {
    const select = document.getElementById('id_article');
    const option = select.options[select.selectedIndex];
    const prix = option.dataset.prix || 0;
    const quantite = document.getElementById('quantite').value || 0;
    
    document.getElementById('prix_unitaire').value = parseInt(prix).toLocaleString() + ' F';
    document.getElementById('total_ligne').value = (prix * quantite).toLocaleString() + ' F';
}

// Selection client en AJAX
document.getElementById('id_client').addEventListener('change', function() {
    const formData = new FormData();
    formData.append('set_client', this.value);
    
    fetch('vente.php', {
        method: 'POST',
        body: formData
    });
});
</script>

<?php include 'pied.php'; ?>