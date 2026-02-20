<?php
include 'connexion.php';

// ================= ARTICLES =================
function getArticle($id = null, $inclure_archives = false)
{
    global $connexion;

    $condition_archive = $inclure_archives ? "" : "AND a.statut = 'actif'";

    if ($id) {
        $sql = "SELECT 
                    a.*, 
                    c.libelle_categorie AS categorie
                FROM article a
                LEFT JOIN categorie_article c ON a.id_categorie = c.id
                WHERE a.id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "SELECT 
                a.*, 
                c.libelle_categorie AS categorie
            FROM article a
            LEFT JOIN categorie_article c ON a.id_categorie = c.id
            WHERE 1=1 $condition_archive
            ORDER BY a.id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
function getArticleArchive() {
    global $connexion;
    $sql = "SELECT * FROM article WHERE statut = 'archive' ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}


 
function getArticleActif() {
    global $connexion;
    $sql = "SELECT * FROM article WHERE statut = 'actif' ORDER BY nom_article ASC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getClient($id = null, $inclure_archives = false)
{
    global $connexion;

    $condition_archive = $inclure_archives ? "" : "AND statut = 'actif'";

    if ($id) {
        $sql = "SELECT * FROM client WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "SELECT * FROM client WHERE 1=1 $condition_archive ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}


function getClientActif() {
    global $connexion;
    $sql = "SELECT * FROM client WHERE statut = 'actif' ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getClientArchive() {
    global $connexion;
    $sql = "SELECT * FROM client WHERE statut = 'archive' ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}



function getVenteById($id) {
    global $connexion;
    
    // Entête + client + vendeur
    $sql = "SELECT v.*, 
                   c.nom AS client_nom, 
                   c.prenom AS client_prenom, 
                   c.telephone, 
                   c.adresse,
                   u.nom AS vendeur_nom,
                   u.prenom AS vendeur_prenom,
                   u.role AS vendeur_role
            FROM vente v
            JOIN client c ON v.id_client = c.id
            LEFT JOIN utilisateur u ON v.id_utilisateur = u.id
            WHERE v.id = ? AND v.etat = '1'";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    $vente = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($vente) {
        // Détails des articles
        $sql = "SELECT d.*, a.nom_article 
                FROM vente_detail d
                JOIN article a ON d.id_article = a.id
                WHERE d.id_vente = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        $vente['articles'] = $req->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $vente;
}


function enregistrerVente($id_client, $panier) {
    global $connexion;
    
    try {
        $connexion->beginTransaction();
        
        // Calculer le total global
        $total_global = 0;
        foreach ($panier as $item) {
            $total_global += $item['prix'] * $item['quantite'];
        }
        
       
        $id_utilisateur = $_SESSION['user']['id'] ?? null;
        
        // Créer la vente (entête) avec id_utilisateur
        $sql = "INSERT INTO vente (id_client, id_utilisateur, total_global) VALUES (?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute([$id_client, $id_utilisateur, $total_global]);
        $id_vente = $connexion->lastInsertId();
        
        // Créer les lignes et mettre à jour le stock
        foreach ($panier as $item) {
            $total_ligne = $item['prix'] * $item['quantite'];
            
            // Insérer le détail
            $sql = "INSERT INTO vente_detail (id_vente, id_article, quantite, prix_unitaire, total_ligne) 
                    VALUES (?, ?, ?, ?, ?)";
            $req = $connexion->prepare($sql);
            $req->execute([
                $id_vente,
                $item['id_article'],
                $item['quantite'],
                $item['prix'],
                $total_ligne
            ]);
            
            // Mettre à jour le stock
            $sql = "UPDATE article SET quantite = quantite - ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$item['quantite'], $item['id_article']]);
        }
        
        $connexion->commit();
        return $id_vente;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        error_log("Erreur vente : " . $e->getMessage());
        return false;
    }
}

/**
 * Annuler une vente (soft delete)
 */
function annulerVente($id_vente) {
    global $connexion;
    
    try {
        $connexion->beginTransaction();
        
        // Récupérer les articles pour remettre le stock
        $sql = "SELECT id_article, quantite FROM vente_detail WHERE id_vente = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id_vente]);
        $articles = $req->fetchAll(PDO::FETCH_ASSOC);
        
        // Remettre le stock
        foreach ($articles as $a) {
            $sql = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$a['quantite'], $a['id_article']]);
        }
        
        // Marquer la vente comme annulée
        $sql = "UPDATE vente SET etat = '0' WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id_vente]);
        
        $connexion->commit();
        return true;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        return false;
    }
}

/**
 * Marquer une vente comme imprimée
 */
function marquerVenteImprimee($id_vente) {
    global $connexion;
    $sql = "UPDATE vente SET imprime = 1, date_impression = NOW() WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id_vente]);
}


function getLastVentes($limit = 10) {
    global $connexion;
    
    $limit = (int)$limit;
    
    $sql = "SELECT v.*, 
                   c.nom AS client_nom, 
                   c.prenom AS client_prenom,
                   u.nom AS vendeur_nom,
                   u.prenom AS vendeur_prenom
            FROM vente v
            JOIN client c ON v.id_client = c.id
            LEFT JOIN utilisateur u ON v.id_utilisateur = u.id
            WHERE v.etat = '1'
            ORDER BY v.date_vente DESC
            LIMIT $limit";
    
    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getTopVentes($limit = 10) {
    global $connexion;
    
    // ✅ Forcer la limite en entier
    $limit = (int)$limit;
    
    $sql = "SELECT 
                a.nom_article,
                SUM(d.quantite) AS total_quantite,
                SUM(d.total_ligne) AS total_ca
            FROM vente_detail d
            JOIN article a ON d.id_article = a.id
            JOIN vente v ON d.id_vente = v.id
            WHERE v.etat = '1'
            GROUP BY a.id, a.nom_article
            ORDER BY total_ca DESC
            LIMIT $limit"; // ✅ Plus de ? ou :limit
    
    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



/**
 * @deprecated Utiliser getVenteById() à la place
 */
function getVente($id = null)
{
    global $connexion;

    if ($id) {
        $sql = "
            SELECT 
                v.id,
                v.id_article,
                a.nom_article,
                c.nom,
                c.prenom,
                v.quantite,
                v.prix,
                v.date_vente,
                v.imprime
            FROM vente v
            JOIN article a ON v.id_article = a.id
            JOIN client c ON v.id_client = c.id
            WHERE v.id = ? AND v.etat = '1'
        ";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    } 
    else {
        $sql = "
            SELECT 
                v.id,
                v.id_article,
                a.nom_article,
                c.nom,
                c.prenom,
                v.quantite,
                v.prix,
                v.date_vente,
                v.imprime
            FROM vente v
            JOIN article a ON v.id_article = a.id
            JOIN client c ON v.id_client = c.id
            WHERE v.etat = '1'
            ORDER BY v.date_vente DESC
        ";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}

/**
 * @deprecated Utiliser getVenteById() à la place
 */
function getRecuVente($id_vente) {
    global $connexion;
    
    $sql = "
        SELECT 
            v.id AS id_vente,
            v.date_vente,
            v.total_global AS prix,
            d.quantite,
            a.nom_article,
            a.prix_unitaire,
            c.nom,
            c.prenom,
            c.telephone,
            c.adresse,
            v.date_impression
        FROM vente v
        JOIN vente_detail d ON v.id = d.id_vente
        JOIN article a ON a.id = d.id_article
        JOIN client c ON c.id = v.id_client
        WHERE v.id = ?
        LIMIT 1
    ";

    $stmt = $connexion->prepare($sql);
    $stmt->execute([$id_vente]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ================= FOURNISSEURS =================
function getFournisseur($id = null)
{
    global $connexion;

    if ($id) {
        $sql = "SELECT * FROM fournisseur WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    } else {
        $sql = "SELECT * FROM fournisseur ORDER BY id DESC";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
function getFournisseurActif() {
    global $connexion;
    $sql = "SELECT * FROM fournisseur WHERE statut = 'actif' ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getFournisseurArchive() {
    global $connexion;
    $sql = "SELECT * FROM fournisseur WHERE statut = 'archive' ORDER BY id DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// ================= COMMANDES =================
function getCommande($id = null)
{
    global $connexion;

    if ($id) {
        $sql = "
            SELECT
                c.id,
                a.nom_article,
                f.nom,
                f.prenom,
                c.quantite,
                c.prix,
                c.date_commande
            FROM commande c
            JOIN article a ON a.id = c.id_article
            JOIN fournisseur f ON f.id = c.id_fournisseur
            WHERE c.id = ?
        ";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    } else {
        $sql = "
            SELECT
                c.id,
                a.nom_article,
                f.nom,
                f.prenom,
                c.quantite,
                c.prix,
                c.date_commande
            FROM commande c
            JOIN article a ON a.id = c.id_article
            JOIN fournisseur f ON f.id = c.id_fournisseur
            ORDER BY c.date_commande DESC
        ";
        $req = $connexion->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
// ================= MODULE COMMANDE =================

/**
 * Generer une reference unique pour une commande
 */
function genererReferenceCommande() {
    $annee = date('Y');
    $mois = date('m');
    
    $sql = "SELECT COUNT(*) as nb FROM commande WHERE YEAR(date_commande) = ? AND MONTH(date_commande) = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$annee, $mois]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    
    $numero = $result['nb'] + 1;
    return 'CMD-' . $annee . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
}


/**
 * Recuperer toutes les commandes
 */
function getAllCommandes() {
    global $connexion;
    $sql = "SELECT c.*, 
                   f.nom AS fournisseur_nom, 
                   f.prenom AS fournisseur_prenom,
                   u.nom AS utilisateur_nom,
                   u.prenom AS utilisateur_prenom
            FROM commande c
            JOIN fournisseur f ON c.id_fournisseur = f.id
            LEFT JOIN utilisateur u ON c.id_utilisateur = u.id
            ORDER BY c.date_commande DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Recuperer une commande avec ses details
 */
function getCommandeById($id) {
    global $connexion;
    
    // Entete de la commande
    $sql = "SELECT c.*, 
                   f.nom AS fournisseur_nom, 
                   f.prenom AS fournisseur_prenom,
                   f.adresse,
                   u.nom AS utilisateur_nom,
                   u.prenom AS utilisateur_prenom
            FROM commande c
            JOIN fournisseur f ON c.id_fournisseur = f.id
            LEFT JOIN utilisateur u ON c.id_utilisateur = u.id
            WHERE c.id = ?";
    $req = $connexion->prepare($sql);
    $req->execute([$id]);
    $commande = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($commande) {
        // Details des articles
        $sql = "SELECT cd.*, a.nom_article 
                FROM commande_detail cd
                JOIN article a ON cd.id_article = a.id
                WHERE cd.id_commande = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        $commande['articles'] = $req->fetchAll(PDO::FETCH_ASSOC);
    }
    
    return $commande;
}

/**
 * Creer une nouvelle commande
 */
function creerCommande($id_fournisseur, $articles, $date_livraison = null, $notes = '') {
    global $connexion;
    
    try {
        $connexion->beginTransaction();
        
        $reference = genererReferenceCommande();
        $total_global = 0;
        foreach ($articles as $a) {
            $total_global += $a['quantite'] * $a['prix_unitaire'];
        }
        
        $sql = "INSERT INTO commande (reference, id_fournisseur, id_utilisateur, date_livraison_prevue, notes, total_global) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute([
            $reference,
            $id_fournisseur,
            $_SESSION['user']['id'] ?? null,
            $date_livraison,
            $notes,
            $total_global
        ]);
        
        $id_commande = $connexion->lastInsertId();
        
        foreach ($articles as $a) {
            $total_ligne = $a['quantite'] * $a['prix_unitaire'];
            $sql = "INSERT INTO commande_detail (id_commande, id_article, quantite, prix_unitaire, total_ligne) 
                    VALUES (?, ?, ?, ?, ?)";
            $req = $connexion->prepare($sql);
            $req->execute([
                $id_commande,
                $a['id_article'],
                $a['quantite'],
                $a['prix_unitaire'],
                $total_ligne
            ]);
        }
        
        $connexion->commit();
        return $id_commande;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        error_log("Erreur creation commande: " . $e->getMessage());
        return false;
    }
}

/**
 * Mettre a jour le statut d'une commande
 */
function updateStatutCommande($id_commande, $nouveau_statut) {
    global $connexion;
    $sql = "UPDATE commande SET statut = ? WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$nouveau_statut, $id_commande]);
}

/**
 * Marquer une commande comme recue (livree)
 */
function recevoirCommande($id_commande) {
    global $connexion;
    
    try {
        $connexion->beginTransaction();
        
        // Recuperer les details de la commande
        $sql = "SELECT cd.*, a.nom_article 
                FROM commande_detail cd
                JOIN article a ON cd.id_article = a.id
                WHERE cd.id_commande = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id_commande]);
        $articles = $req->fetchAll(PDO::FETCH_ASSOC);
        
        // Ajouter chaque article au stock
        foreach ($articles as $a) {
            $sql = "UPDATE article SET quantite = quantite + ? WHERE id = ?";
            $req = $connexion->prepare($sql);
            $req->execute([$a['quantite'], $a['id_article']]);
            
            // Enregistrer le mouvement de stock
            $sql = "INSERT INTO mouvement_stock 
                    (id_article, type, quantite, stock_avant, stock_apres, motif) 
                    VALUES (?, 'entree', ?, 
                    (SELECT quantite FROM article WHERE id = ?) - ?, 
                    (SELECT quantite FROM article WHERE id = ?), 
                    ?)";
            $req = $connexion->prepare($sql);
            $req->execute([
                $a['id_article'],
                $a['quantite'],
                $a['id_article'],
                $a['quantite'],
                $a['id_article'],
                'Reception commande #' . $id_commande
            ]);
        }
        
        // Mettre a jour la commande
        $sql = "UPDATE commande SET statut = 'livree', date_livraison_reelle = NOW() WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id_commande]);
        
        $connexion->commit();
        return true;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        error_log("Erreur reception commande: " . $e->getMessage());
        return false;
    }
}

/**
 * Annuler une commande
 */
function annulerCommande($id_commande) {
    global $connexion;
    $sql = "UPDATE commande SET statut = 'annulee' WHERE id = ?";
    $req = $connexion->prepare($sql);
    return $req->execute([$id_commande]);
}

/**
 * Compter les commandes par statut
 */
function countCommandesByStatut() {
    global $connexion;
    $sql = "SELECT statut, COUNT(*) as nombre, SUM(total_global) as total
            FROM commande
            GROUP BY statut";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCommande()
{
    $sql ="SELECT COUNT(*) AS nbre FROM commande";
    $req=$GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetch();
}

// ================= STATISTIQUES =================
function getAllVente() {
    global $connexion;
    $sql = "SELECT v.*, 
                   c.nom AS client_nom, 
                   c.prenom AS client_prenom,
                   u.nom AS vendeur_nom,
                   u.prenom AS vendeur_prenom
            FROM vente v
            JOIN client c ON v.id_client = c.id
            LEFT JOIN utilisateur u ON v.id_utilisateur = u.id
            WHERE v.etat = '1'
            ORDER BY v.date_vente DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    
    // ✅ Si vide, retourne un tableau vide (évite les erreurs)
    return $result ?: [];
}
function getVentesDuJour() {
    global $connexion;
    $sql = "SELECT v.*, c.nom AS client_nom, c.prenom AS client_prenom 
            FROM vente v
            JOIN client c ON v.id_client = c.id
            WHERE v.etat = '1' AND DATE(v.date_vente) = CURDATE()
            ORDER BY v.date_vente DESC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Rechercher des ventes par nom du client
 */
function rechercherVentesParClient($search) {
    global $connexion;
    $sql = "SELECT v.*, 
                   c.nom AS client_nom, 
                   c.prenom AS client_prenom,
                   u.nom AS vendeur_nom,
                   u.prenom AS vendeur_prenom
            FROM vente v
            JOIN client c ON v.id_client = c.id
            LEFT JOIN utilisateur u ON v.id_utilisateur = u.id
            WHERE v.etat = '1' 
            AND (c.nom LIKE ? OR c.prenom LIKE ? OR CONCAT(c.prenom, ' ', c.nom) LIKE ?)
            ORDER BY v.date_vente DESC";
    
    $searchTerm = "%$search%";
    $req = $connexion->prepare($sql);
    $req->execute([$searchTerm, $searchTerm, $searchTerm]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
function getAllArticle()
{
    $sql ="SELECT COUNT(*) AS nbre FROM article";
    $req=$GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetch();
}

function getCA() 
{
    global $connexion;
    $sql = "SELECT SUM(total_global) AS prix FROM vente WHERE etat = '1'";
    $stmt = $connexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['prix'] ? (float) $result['prix'] : 0;
}

// ================= CATÉGORIES =================
function getCategorie($id = null)
{
    global $connexion;

    if ($id) {
        $sql = "SELECT * FROM categorie_article WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$id]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    $sql = "SELECT * FROM categorie_article ORDER BY libelle_categorie ASC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// ================= MODULE STOCK =================
function getTotalArticles() {
    $sql = "SELECT COUNT(*) AS total FROM article WHERE statut = 'actif'";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getStockFaible() {
    $sql = "SELECT COUNT(*) AS total 
            FROM article 
            WHERE statut = 'actif' 
            AND quantite <= seuil_alerte 
            AND quantite > 0";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getRuptureStock() {
    $sql = "SELECT COUNT(*) AS total 
            FROM article 
            WHERE statut = 'actif' 
            AND quantite = 0";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getValeurStock() {
    $sql = "SELECT SUM(quantite * prix_unitaire) AS total 
            FROM article 
            WHERE statut = 'actif'";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'] ?? 0;
}

function getArticlesStock() {
    $sql = "SELECT 
                a.id,
                a.nom_article,
                a.prix_unitaire,
                a.quantite,
                a.seuil_alerte,
                COALESCE(c.libelle_categorie, 'Non catégorisé') AS categorie
            FROM article a
            LEFT JOIN categorie_article c ON a.id_categorie = c.id
            WHERE a.statut = 'actif'
            ORDER BY 
                CASE 
                    WHEN a.quantite = 0 THEN 1
                    WHEN a.quantite <= a.seuil_alerte THEN 2
                    ELSE 3
                END,
                a.nom_article ASC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getAlertesStock() {
    $sql = "SELECT 
                a.id,
                a.nom_article,
                a.quantite,
                a.seuil_alerte,
                CASE 
                    WHEN a.quantite = 0 THEN 'rupture'
                    ELSE 'faible'
                END AS statut
            FROM article a
            WHERE a.statut = 'actif'
            AND (a.quantite = 0 OR a.quantite <= a.seuil_alerte)
            ORDER BY 
                CASE 
                    WHEN a.quantite = 0 THEN 1
                    ELSE 2
                END,
                a.quantite ASC
            LIMIT 10";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// ================= MOUVEMENTS DE STOCK =================
function ajouterEntreeStock($id_article, $quantite, $motif = 'Entrée de stock') {
    $connexion = $GLOBALS['connexion'];
    
    try {
        $connexion->beginTransaction();
        
        $sql = "SELECT quantite FROM article WHERE id = ? AND statut = 'actif'";
        $req = $connexion->prepare($sql);
        $req->execute([$id_article]);
        $article = $req->fetch(PDO::FETCH_ASSOC);
        
        if (!$article) {
            return false;
        }
        
        $stock_avant = $article['quantite'];
        $stock_apres = $stock_avant + $quantite;
        
        $sql = "UPDATE article SET quantite = ? WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$stock_apres, $id_article]);
        
        $sql = "INSERT INTO mouvement_stock 
                (id_article, type, quantite, stock_avant, stock_apres, motif) 
                VALUES (?, 'entree', ?, ?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute([$id_article, $quantite, $stock_avant, $stock_apres, $motif]);
        
        $connexion->commit();
        return true;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        return false;
    }
}

function ajouterSortieStock($id_article, $quantite, $motif = 'Sortie de stock') {
    $connexion = $GLOBALS['connexion'];
    
    try {
        $connexion->beginTransaction();
        
        $sql = "SELECT quantite FROM article WHERE id = ? AND statut = 'actif'";
        $req = $connexion->prepare($sql);
        $req->execute([$id_article]);
        $article = $req->fetch(PDO::FETCH_ASSOC);
        
        if (!$article) {
            return false;
        }
        
        $stock_avant = $article['quantite'];
        
        if ($stock_avant < $quantite) {
            return false;
        }
        
        $stock_apres = $stock_avant - $quantite;
        
        $sql = "UPDATE article SET quantite = ? WHERE id = ?";
        $req = $connexion->prepare($sql);
        $req->execute([$stock_apres, $id_article]);
        
        $sql = "INSERT INTO mouvement_stock 
                (id_article, type, quantite, stock_avant, stock_apres, motif) 
                VALUES (?, 'sortie', ?, ?, ?, ?)";
        $req = $connexion->prepare($sql);
        $req->execute([$id_article, $quantite, $stock_avant, $stock_apres, $motif]);
        
        $connexion->commit();
        return true;
        
    } catch (Exception $e) {
        $connexion->rollBack();
        return false;
    }
}

function getMouvementsStock($limit = 20) {
    $sql = "SELECT 
                m.*,
                a.nom_article
            FROM mouvement_stock m
            JOIN article a ON m.id_article = a.id
            ORDER BY m.date_mouvement DESC
            LIMIT ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$limit]);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

function getArticleById($id) {
    $sql = "SELECT * FROM article WHERE id = ? AND statut = 'actif'";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function getMouvementsStockFiltres($filtres = []) {
    $connexion = $GLOBALS['connexion'];
    
    $sql = "SELECT 
                m.*,
                a.nom_article
            FROM mouvement_stock m
            JOIN article a ON m.id_article = a.id
            WHERE 1=1";
    
    $params = [];
    
    if (!empty($filtres['id_article'])) {
        $sql .= " AND m.id_article = ?";
        $params[] = $filtres['id_article'];
    }
    
    if (!empty($filtres['type'])) {
        $sql .= " AND m.type = ?";
        $params[] = $filtres['type'];
    }
    
    if (!empty($filtres['date_debut'])) {
        $sql .= " AND DATE(m.date_mouvement) >= ?";
        $params[] = $filtres['date_debut'];
    }
    
    if (!empty($filtres['date_fin'])) {
        $sql .= " AND DATE(m.date_mouvement) <= ?";
        $params[] = $filtres['date_fin'];
    }
    
    $sql .= " ORDER BY m.date_mouvement DESC LIMIT 500";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

// ================= MODULE UTILISATEURS =================
function verifierAdminExiste() {
    $sql = "SELECT COUNT(*) as nbre FROM utilisateur WHERE role = 'admin'";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['nbre'] > 0;
}

function loginExiste($login) {
    $sql = "SELECT COUNT(*) as nbre FROM utilisateur WHERE login = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$login]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['nbre'] > 0;
}

function emailExiste($email) {
    $sql = "SELECT COUNT(*) as nbre FROM utilisateur WHERE email = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$email]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['nbre'] > 0;
}

function verifierConnexion($login, $password) {
    $sql = "SELECT * FROM utilisateur WHERE login = ? AND statut = 'actif'";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$login]);
    $user = $req->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $sql = "UPDATE utilisateur SET derniere_connexion = NOW() WHERE id = ?";
        $req = $GLOBALS['connexion']->prepare($sql);
        $req->execute([$user['id']]);
        
        unset($user['password']);
        return $user;
    }
    return false;
}

function creerUtilisateur($data) {
    $sql = "INSERT INTO utilisateur 
            (nom, prenom, email, telephone, login, password, role, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $req = $GLOBALS['connexion']->prepare($sql);
    
    $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
    $created_by = $_SESSION['user']['id'] ?? null;
    
    $result = $req->execute([
        $data['nom'],
        $data['prenom'],
        $data['email'],
        $data['telephone'] ?? null,
        $data['login'],
        $password_hash,
        $data['role'],
        $created_by
    ]);
    
    if ($result) {
        return $GLOBALS['connexion']->lastInsertId();
    }
    return false;
}

function getUtilisateursActifs() {
    $sql = "SELECT id, nom, prenom, email, telephone, login, role, date_creation, derniere_connexion 
            FROM utilisateur 
            WHERE statut = 'actif' 
            ORDER BY id DESC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
function getUtilisateursArchive() {
    $sql = "SELECT id, nom, prenom, email, telephone, login, role, date_creation, derniere_connexion 
            FROM utilisateur 
            WHERE statut = 'archive' 
            ORDER BY id DESC";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
function getUtilisateurById($id) {
    $sql = "SELECT id, nom, prenom, email, telephone, login, role, statut 
            FROM utilisateur WHERE id = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$id]);
    return $req->fetch(PDO::FETCH_ASSOC);
}

function modifierMonLogin($id, $nouveau_login) {
    if (loginExiste($nouveau_login)) {
        return false;
    }
    $sql = "UPDATE utilisateur SET login = ? WHERE id = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    return $req->execute([$nouveau_login, $id]);
}

function modifierMonMotDePasse($id, $ancien_password, $nouveau_password) {
    $sql = "SELECT password FROM utilisateur WHERE id = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$id]);
    $user = $req->fetch(PDO::FETCH_ASSOC);
    
    if (!password_verify($ancien_password, $user['password'])) {
        return false;
    }
    
    $password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
    $sql = "UPDATE utilisateur SET password = ? WHERE id = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    return $req->execute([$password_hash, $id]);
}

// ================= GESTION DES RÔLES =================
function estConnecte() {
    return isset($_SESSION['user']);
}

function aRole($role) {
    return estConnecte() && $_SESSION['user']['role'] === $role;
}

function estAdmin() {
    return aRole('admin');
}

function estGestionnaire() {
    return aRole('gestionnaire');
}

function estCaissier() {
    return aRole('caissier');
}

function peutGererStock() {
    return estConnecte() && (estAdmin() || estGestionnaire());
}

function peutVendre() {
    return estConnecte();
}

function peutGererUtilisateurs() {
    return estAdmin();
}

function peutConfigurer() {
    return estAdmin();
}

function requireAuth() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit;
    }
    if (!estAdmin()) {
        $_SESSION['erreur'] = " Accès refusé. Vous devez être administrateur.";
        header('Location: dashboard.php');
        exit;
    }
}

function requireGestionnaire() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit;
    }
    if (!peutGererStock()) {
        $_SESSION['erreur'] = " Accès refusé. Vous n'avez pas les droits nécessaires.";
        header('Location: dashboard.php');
        exit;
    }
}

function requireVente() {
    if (!estConnecte()) {
        header('Location: login.php');
        exit;
    }
}

// ================= MODULE CONFIGURATION =================
function getConfig($cle, $default = '') {
    $sql = "SELECT valeur FROM configuration WHERE cle = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    $req->execute([$cle]);
    $res = $req->fetch(PDO::FETCH_ASSOC);
    return $res ? $res['valeur'] : $default;
}

function setConfig($cle, $valeur) {
    $sql = "UPDATE configuration SET valeur = ? WHERE cle = ?";
    $req = $GLOBALS['connexion']->prepare($sql);
    return $req->execute([$valeur, $cle]);
}

function formatNumeroRecu($numero) {
    $format = getConfig('format_recu', 'ESP-{annee}-{numero}');
    $annee = date('Y');
    $format = str_replace('{annee}', $annee, $format);
    $format = str_replace('{numero}', str_pad($numero, 5, '0', STR_PAD_LEFT), $format);
    return $format;
}

// ================= UPLOAD LOGO =================
function uploadLogo($fichier) {
    $dossier = __DIR__ . '/../public/uploads/logo/';
    
    if (!file_exists($dossier)) {
        mkdir($dossier, 0777, true);
    }
    
    if (!is_writable($dossier)) {
        error_log(" Dossier non accessible en écriture : " . $dossier);
        return ['success' => false, 'message' => ' Erreur serveur : dossier non accessible'];
    }
    
    $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $extensions)) {
        return ['success' => false, 'message' => ' Format non autorisé (jpg, png, gif, webp)'];
    }
    
    if ($fichier['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'message' => ' Logo trop grand (max 2 Mo)'];
    }
    
    $nom_fichier = 'logo_' . date('Ymd_His') . '.' . $extension;
    $chemin_complet = $dossier . $nom_fichier;
    
    if (move_uploaded_file($fichier['tmp_name'], $chemin_complet)) {
        setConfig('entreprise_logo', 'uploads/logo/' . $nom_fichier);
        
        $ancien_logo = getConfig('entreprise_logo');
        if ($ancien_logo && $ancien_logo != 'logo-removebg-preview.png') {
            $ancien_chemin = __DIR__ . '/../public/' . $ancien_logo;
            if (file_exists($ancien_chemin)) {
                unlink($ancien_chemin);
            }
        }
        
        return ['success' => true, 'message' => ' Logo uploadé avec succès'];
    } else {
        $erreur = $_FILES['logo']['error'] ?? 'Erreur inconnue';
        error_log(" Erreur upload : " . $erreur);
        return ['success' => false, 'message' => ' Erreur lors du déplacement du fichier'];
    }
}
// ========== DONNÉES POUR GRAPHIQUES ==========

/**
 * Ventes des 7 derniers jours
 */
function getVentes7Jours() {
    global $connexion;
    $sql = "SELECT DATE(date_vente) as jour, COUNT(*) as nb_ventes, SUM(total_global) as ca
            FROM vente
            WHERE etat = '1' AND date_vente >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(date_vente)
            ORDER BY jour ASC";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Répartition des ventes par catégorie
 */
function getVentesParCategorie() {
    global $connexion;
    $sql = "SELECT c.libelle_categorie, COUNT(d.id) as nb_ventes, SUM(d.total_ligne) as ca
            FROM vente_detail d
            JOIN article a ON d.id_article = a.id
            JOIN categorie_article c ON a.id_categorie = c.id
            JOIN vente v ON d.id_vente = v.id
            WHERE v.etat = '1'
            GROUP BY c.id, c.libelle_categorie
            ORDER BY ca DESC
            LIMIT 5";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Top 5 articles les plus vendus (quantité)
 */
function getTop5Articles() {
    global $connexion;
    $sql = "SELECT a.nom_article, SUM(d.quantite) as total_qte
            FROM vente_detail d
            JOIN article a ON d.id_article = a.id
            JOIN vente v ON d.id_vente = v.id
            WHERE v.etat = '1'
            GROUP BY a.id, a.nom_article
            ORDER BY total_qte DESC
            LIMIT 5";
    $req = $connexion->prepare($sql);
    $req->execute();
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
/**
 * Filtrer les ventes selon plusieurs critères
 */
function filtrerVentes($filtres) {
    global $connexion;
    
    $sql = "SELECT v.*, 
                   c.nom AS client_nom, 
                   c.prenom AS client_prenom,
                   u.nom AS vendeur_nom,
                   u.prenom AS vendeur_prenom
            FROM vente v
            JOIN client c ON v.id_client = c.id
            LEFT JOIN utilisateur u ON v.id_utilisateur = u.id
            WHERE v.etat = '1'";
    
    $params = [];
    
    // Filtre par date
    if (!empty($filtres['filtre_date'])) {
        switch($filtres['filtre_date']) {
            case 'aujourdhui':
                $sql .= " AND DATE(v.date_vente) = CURDATE()";
                break;
            case 'hier':
                $sql .= " AND DATE(v.date_vente) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'semaine':
                $sql .= " AND YEARWEEK(v.date_vente) = YEARWEEK(CURDATE())";
                break;
            case 'mois':
                $sql .= " AND MONTH(v.date_vente) = MONTH(CURDATE()) AND YEAR(v.date_vente) = YEAR(CURDATE())";
                break;
            case 'personnalise':
                if (!empty($filtres['date_debut'])) {
                    $sql .= " AND DATE(v.date_vente) >= ?";
                    $params[] = $filtres['date_debut'];
                }
                if (!empty($filtres['date_fin'])) {
                    $sql .= " AND DATE(v.date_vente) <= ?";
                    $params[] = $filtres['date_fin'];
                }
                break;
        }
    }
    
    // Filtre par client
    if (!empty($filtres['client'])) {
        $sql .= " AND v.id_client = ?";
        $params[] = $filtres['client'];
    }
    
    // Filtre par vendeur
    if (!empty($filtres['vendeur'])) {
        $sql .= " AND v.id_utilisateur = ?";
        $params[] = $filtres['vendeur'];
    }
    
    // Filtre par statut
    if (!empty($filtres['statut'])) {
        if ($filtres['statut'] == 'imprime') {
            $sql .= " AND v.imprime = 1";
        } elseif ($filtres['statut'] == 'non_imprime') {
            $sql .= " AND v.imprime = 0";
        }
    }
    
    $sql .= " ORDER BY v.date_vente DESC";
    
    $req = $connexion->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_ASSOC);
}
?>