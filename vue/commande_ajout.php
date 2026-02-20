<?php
include 'entete.php';
require_once '../model/fonction.php';
requireAuth();

$fournisseurs = getFournisseurActif();
$articles = getArticleActif();

// Initialiser le panier de commande en session
if (!isset($_SESSION['panier_commande'])) {
    $_SESSION['panier_commande'] = [];
}

// Ajouter un article au panier
if (isset($_POST['ajouter_panier'])) {
    $id_article = $_POST['id_article'];
    $quantite = (int)$_POST['quantite'];
    $prix = (int)$_POST['prix_unitaire'];
    
    $sql = "SELECT * FROM article WHERE id = ? AND statut = 'actif'";
    $req = $connexion->prepare($sql);
    $req->execute([$id_article]);
    $article = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($article) {
        $trouve = false;
        foreach ($_SESSION['panier_commande'] as &$item) {
            if ($item['id_article'] == $id_article) {
                $item['quantite'] += $quantite;
                $item['total'] = $item['quantite'] * $item['prix'];
                $trouve = true;
                break;
            }
        }
        
        if (!$trouve) {
            $_SESSION['panier_commande'][] = [
                'id_article' => $article['id'],
                'nom_article' => $article['nom_article'],
                'prix' => $prix,
                'quantite' => $quantite,
                'total' => $prix * $quantite
            ];
        }
        
        $_SESSION['message'] = ['text' => 'Article ajoute au panier', 'type' => 'success'];
    }
    
    ?>
    <script>window.location.href = 'commande_ajout.php';</script>
    <?php
    exit;
}

// Supprimer un article du panier
if (isset($_GET['supprimer'])) {
    $index = (int)$_GET['supprimer'];
    if (isset($_SESSION['panier_commande'][$index])) {
        unset($_SESSION['panier_commande'][$index]);
        $_SESSION['panier_commande'] = array_values($_SESSION['panier_commande']);
    }
    ?>
    <script>window.location.href = 'commande_ajout.php';</script>
    <?php
    exit;
}

// Vider le panier
if (isset($_GET['vider'])) {
    $_SESSION['panier_commande'] = [];
    ?>
    <script>window.location.href = 'commande_ajout.php';</script>
    <?php
    exit;
}

// Valider la commande
if (isset($_POST['valider_commande'])) {
    $id_fournisseur = (int)$_POST['id_fournisseur'];
    $date_livraison = $_POST['date_livraison'] ?? null;
    $notes = $_POST['notes'] ?? '';
    $panier = $_SESSION['panier_commande'];
    
    if (empty($panier)) {
        $_SESSION['message'] = ['text' => 'Panier vide', 'type' => 'danger'];
    } elseif (!$id_fournisseur) {
        $_SESSION['message'] = ['text' => 'Veuillez selectionner un fournisseur', 'type' => 'danger'];
    } else {
        // Preparer les articles pour la fonction creerCommande
        $articles_commande = [];
        foreach ($panier as $item) {
            $articles_commande[] = [
                'id_article' => $item['id_article'],
                'quantite' => $item['quantite'],
                'prix_unitaire' => $item['prix']
            ];
        }
        
        $id_commande = creerCommande($id_fournisseur, $articles_commande, $date_livraison, $notes);
        
        if ($id_commande) {
            $_SESSION['panier_commande'] = [];
            $_SESSION['message'] = ['text' => 'Commande creee avec succes', 'type' => 'success'];
            ?>
            <script>window.location.href = 'commande_details.php?id=<?= $id_commande ?>';</script>
            <?php
        } else {
            $_SESSION['message'] = ['text' => 'Erreur lors de la creation', 'type' => 'danger'];
            ?>
            <script>window.location.href = 'commande_ajout.php';</script>
            <?php
        }
        exit;
    }
}

$panier = $_SESSION['panier_commande'];

// Calculer le total du panier
$total_panier = 0;
foreach ($panier as $item) {
    $total_panier += $item['total'];
}
?>

<style>
.ajout-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 24px;
    color: #1a2b3c;
}

.ajout-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.ajout-card {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f0f0f0;
}

.card-header {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f5f5f5;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: #1a2b3c;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
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

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #5f6b7a;
    font-size: 13px;
    text-transform: uppercase;
}

.form-group select,
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s;
}

.form-group select:focus,
.form-group input:focus,
.form-group textarea:focus {
    border-color: #0b5e2e;
    outline: none;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.article-row {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr auto;
    gap: 15px;
    align-items: end;
    margin-bottom: 20px;
}

.btn-add-article {
    background: #0b5e2e;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.3s;
    height: 48px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-add-article:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.panier-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.btn-clear {
    background: #fee9e7;
    color: #b3261e;
    padding: 8px 16px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 500;
}

.panier-vide {
    text-align: center;
    padding: 40px;
    color: #8f9baf;
    background: #f8f9fa;
    border-radius: 12px;
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
    border-top: 2px solid #e0e0e0;
}

.btn-remove {
    color: #b3261e;
    text-decoration: none;
    font-size: 18px;
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

.btn-validate:hover {
    background: #0a4f26;
    transform: translateY(-2px);
}

.btn-validate:disabled {
    background: #e0e0e0;
    cursor: not-allowed;
}

.text-right {
    text-align: right;
}

@media (max-width: 992px) {
    .ajout-row {
        grid-template-columns: 1fr;
    }
    
    .article-row {
        grid-template-columns: 1fr;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="home-content">
    <div class="ajout-container">
        
        <!-- Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert <?= $_SESSION['message']['type'] ?>">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <!-- En-tete -->
        <div class="page-header">
            <h1>
                <i class="bx bx-plus-circle"></i>
                Nouvelle commande fournisseur
            </h1>
        </div>

        <!-- Layout 2 colonnes -->
        <div class="ajout-row">
            <!-- Colonne gauche : Informations commande -->
            <div class="ajout-card">
                <div class="card-header">
                    <h3>
                        <i class="bx bx-info-circle"></i>
                        Informations commande
                    </h3>
                </div>

                <form method="POST" id="formCommande">
                    <div class="form-group">
                        <label>Fournisseur</label>
                        <select name="id_fournisseur" id="id_fournisseur" required>
                            <option value="">-- Choisir un fournisseur --</option>
                            <?php foreach ($fournisseurs as $f): ?>
                                <option value="<?= $f['id'] ?>">
                                    <?= htmlspecialchars($f['prenom'] . ' ' . $f['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Date livraison prevue</label>
                            <input type="date" name="date_livraison" min="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <input type="text" readonly value="Optionnel" style="background:#f5f5f5;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Notes (optionnel)</label>
                        <textarea name="notes" rows="3" placeholder="Informations complementaires..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Colonne droite : Ajout articles -->
            <div class="ajout-card">
                <div class="card-header">
                    <h3>
                        <i class="bx bx-cart-add"></i>
                        Ajouter des articles
                    </h3>
                </div>

                <form method="POST">
                    <div class="article-row">
                        <div class="form-group">
                            <label>Article</label>
                            <select name="id_article" id="id_article" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach ($articles as $a): ?>
                                    <option value="<?= $a['id'] ?>" 
                                            data-prix="<?= $a['prix_unitaire'] ?>">
                                        <?= htmlspecialchars($a['nom_article']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Quantite</label>
                            <input type="number" name="quantite" id="quantite" min="1" value="1" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Prix unitaire</label>
                            <input type="number" name="prix_unitaire" id="prix_unitaire" min="0" required>
                        </div>
                        
                        <button type="submit" name="ajouter_panier" class="btn-add-article">
                            <i class="bx bx-plus"></i> Ajouter
                        </button>
                    </div>
                </form>

                <div class="panier-header">
                    <h4>Articles commandes</h4>
                    <?php if (!empty($panier)): ?>
                        <a href="commande_ajout.php?vider=1" class="btn-clear" onclick="return confirm('Vider le panier ?')">
                            Vider
                        </a>
                    <?php endif; ?>
                </div>

                <?php if (empty($panier)): ?>
                    <div class="panier-vide">
                        <i class="bx bx-cart" style="font-size: 48px;"></i>
                        <p>Aucun article ajoute</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="panier-table">
                            <thead>
                                <tr>
                                    <th>Article</th>
                                    <th class="text-right">Prix unitaire</th>
                                    <th class="text-center">Quantite</th>
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
                                    <td class="text-right"><?= number_format($item['total'], 0, ',', ' ') ?> F</td>
                                    <td>
                                        <a href="commande_ajout.php?supprimer=<?= $index ?>" 
                                           class="btn-remove" 
                                           onclick="return confirm('Retirer ?')">
                                            <i class="bx bx-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Total commande</strong></td>
                                    <td class="text-right"><strong><?= number_format($total_panier, 0, ',', ' ') ?> F</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <button type="submit" name="valider_commande" form="formCommande" 
                            class="btn-validate" <?= empty($panier) ? 'disabled' : '' ?>>
                        <i class="bx bx-check-circle"></i>
                        Valider la commande
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Mise a jour du prix quand on change l'article
document.getElementById('id_article').addEventListener('change', function() {
    const select = this;
    const option = select.options[select.selectedIndex];
    const prix = option.dataset.prix || 0;
    document.getElementById('prix_unitaire').value = prix;
});
</script>

<?php include 'pied.php'; ?>