<?php
include 'entete.php';
 if (isset($_SESSION['message'])): ?>
    <div class="alert <?= $_SESSION['message']['type'] ?>">
        <?= $_SESSION['message']['text'] ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; 
require_once '../model/fonction.php';
requireAuth();

// Récupérer la catégorie si on est en modification
$categorie = null;
if (!empty($_GET['id'])) {
    $categorie = getCategorie($_GET['id']);
}
?>

<div class="home-content">
    <div class="overview-boxes">
        <div class="box">
            
           <form action="<?= !empty($_GET['id']) ? "../model/modificationCategorie.php" : "../model/ajoutCategorie.php" ?>" method="POST">

                <label for="libelle_categorie">Nom Catégorie </label>
                <input 
                    type="text" 
                    name="libelle_categorie" 
                    placeholder="Nom de la catégorie" 
                    required
                    value="<?= $categorie ? htmlspecialchars($categorie['libelle_categorie']) : '' ?>"
                >
                
                <?php if (!empty($_GET['id'])): ?>
                    <input type="hidden" name="id" value="<?= $categorie['id'] ?>">
                <?php endif; ?>

                <button type="submit"><?= $categorie ? 'Modifier' : 'Ajouter' ?></button>
            </form>
        </div>

        <div class="table">
            <table class="mtable">
                <tr>
                    <th>Nom catégorie</th>
                    <th>Actions</th>
                </tr>

                <?php
                $categories = getCategorie(); // récupère toutes les catégories
                if (!empty($categories) && is_array($categories)):
                    foreach ($categories as $value):
                ?>
                <tr>
                    <td><?= htmlspecialchars($value['libelle_categorie']) ?></td>
                    <td>
                        <a href="?id=<?= $value['id'] ?>" class="btn-edit"><i class="bx bx-edit"></i></a>
                        <a href="../model/supprimerCategorie.php?id=<?= $value['id'] ?>" 
                            onclick="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');" 
                            >
                            <i class="bx bx-trash"></i>  
                        </a>

                    </td>
                </tr>
                <?php
                    endforeach;
                endif;
                ?>
            </table>
        </div>
    </div>
</div>

<?php
include 'pied.php';
?>
