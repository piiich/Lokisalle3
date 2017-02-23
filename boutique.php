<?php
// Important !!!!
require_once('inc/init.inc.php');
// Tout les traitements php
// Traitement pour récupérer toutes les catégories
$resultat = $pdo -> query("SELECT DISTINCT categorie FROM salle");
$categorie = $resultat -> fetchAll(PDO::FETCH_ASSOC);
// Grâce à fetchAll(), $catégorie est un array multidimentionnel avec les infos de chaques catégories. A l'indice catégorie je retrouve le nom de ma catégorie
// debug($categorie);

// Traitement pour récupérer tous les salles par catégories (ou par défaut tous les salles du site)
if (isset($_GET['categorie']) && $_GET['categorie'] !='') {
    $resultat = $pdo -> prepare("SELECT * FROM salle WHERE categorie = :categorie");
    $resultat ->bindParam(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $resultat -> execute();

    if ($resultat -> rowCount() > 0) {
        $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        $resultat = $pdo -> query("SELECT * FROM salle");
        $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
        // Si on est dans ce ELSE cela signifie que notre rquête n'a rien trouvé concernant cette catégorie.
        // L'utilisateur à certainement modifié l'URL (cas exeptionnel entre l'arrivée sur cette page et le click, on a plus de stock dans cette catégorie)
        // Dans ce cas on peut soit recharger la page, rediriger vers une 404, soit efféctuer une recherche générique avec tous les salles
    }
}
else{
    $resultat = $pdo -> query("SELECT * FROM salle");
    $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    // On est dans ce ELSE, s'il n'y a pas de paramétre catégorie dans l4url (quand on arrive sur cette page) ou alors si le paramèrte catégorie est vide
}

// Traitement pour récupérer toutes les prix
$resultat = $pdo -> query("SELECT DISTINCT ville FROM salle");
$ville = $resultat -> fetchAll(PDO::FETCH_ASSOC);
// Grâce à fetchAll(), $catégorie est un array multidimentionnel avec les infos de chaques catégories. A l'indice catégorie je retrouve le nom de ma catégorie
// debug($ville);

// Traitement pour récupérer tous les salles par catégories (ou par défaut tous les salles du site)
if (isset($_GET['ville']) && $_GET['ville'] !='') {
    $resultat = $pdo -> prepare("SELECT * FROM salle WHERE ville = :ville");
    $resultat ->bindParam(':ville', $_GET['ville'], PDO::PARAM_STR);
    $resultat -> execute();

    if ($resultat -> rowCount() > 0) {
        $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        $resultat = $pdo -> query("SELECT * FROM salle");
        $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
        // Si on est dans ce ELSE cela signifie que notre rquête n'a rien trouvé concernant cette catégorie.
        // L'utilisateur à certainement modifié l'URL (cas exeptionnel entre l'arrivée sur cette page et le click, on a plus de stock dans cette catégorie)
        // Dans ce cas on peut soit recharger la page, rediriger vers une 404, soit efféctuer une recherche générique avec tous les salles
    }
}
else{
    $resultat = $pdo -> query("SELECT * FROM salle");
    $salles = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    // On est dans ce ELSE, s'il n'y a pas de paramétre catégorie dans l4url (quand on arrive sur cette page) ou alors si le paramèrte catégorie est vide
}

// Traitement pour récupérer toutes les prix
$resultat = $pdo -> query("SELECT DISTINCT prix FROM produit");
$prix = $resultat -> fetchAll(PDO::FETCH_ASSOC);
// Grâce à fetchAll(), $catégorie est un array multidimentionnel avec les infos de chaques catégories. A l'indice catégorie je retrouve le nom de ma catégorie
// debug($prix);

// Traitement pour récupérer tous les produits par catégories (ou par défaut tous les produits du site)
if (isset($_GET['prix']) && $_GET['prix'] !='') {
    $resultat = $pdo -> prepare("SELECT * FROM produit WHERE prix = :prix");
    $resultat ->bindParam(':prix', $_GET['prix'], PDO::PARAM_STR);
    $resultat -> execute();

    if ($resultat -> rowCount() > 0) {
        $produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    }
    else{
        $resultat = $pdo -> query("SELECT * FROM produit");
        $produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
        // Si on est dans ce ELSE cela signifie que notre rquête n'a rien trouvé concernant cette catégorie.
        // L'utilisateur à certainement modifié l'URL (cas exeptionnel entre l'arrivée sur cette page et le click, on a plus de stock dans cette catégorie)
        // Dans ce cas on peut soit recharger la page, rediriger vers une 404, soit efféctuer une recherche générique avec tous les produits
    }
}
else{
    $resultat = $pdo -> query("SELECT * FROM produit");
    $produits = $resultat -> fetchAll(PDO::FETCH_ASSOC);
    // On est dans ce ELSE, s'il n'y a pas de paramétre catégorie dans l4url (quand on arrive sur cette page) ou alors si le paramèrte catégorie est vide
}

$page="Accès à la boutique";
//  Appel du header
require_once('inc/header.inc.php');
?>
    <!-- Mon contenu HTML -->
    <h1>Boutique</h1>
    <div class="boutique-gauche">
        <ul>
            <?php foreach ($categorie as $valeur) : ?>
                <li><a href="?categorie=<?= $valeur['categorie'] ?>"><?= $valeur['categorie'] ?></a></li>
            <?php endforeach ; ?>
        </ul>
    </div>
    <div class="boutique-gauche">
        <ul>
            <?php foreach ($ville as $valeur) : ?>
                <li><a href="?ville=<?= $valeur['ville'] ?>"><?= $valeur['ville'] ?></a></li>
            <?php endforeach ; ?>
        </ul>
    </div>
    <div class="boutique-gauche">
        <ul>
            <?php foreach ($prix as $valeur) : ?>
                <li><a href="?prix=<?= $valeur['prix'] ?>"><?= $valeur['prix'] ?></a></li>
            <?php endforeach ; ?>
        </ul>
    </div>

    <div class="boutique-droite">

        <?php foreach($produits as $valeur) : ?>
            <!-- vignette produit -->
            <div class="boutique-produit">
                <h3><?= $valeur['titre'] ?></h3>
                <a href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>"><img src="photo/<?= $valeur['photo'] ?>" height="100"></a>
                <p style="font-weight: bold; font-size: 20px;"><?= $valeur['prix'] ?>€</p>
                <p style="height: 40px"><?= $valeur['description'] ?></p><br>
                <a style="padding: 5px; background: #fff; text-align: center;border: 2px solid black; border-radius: 3px" href="fiche_produit.php?id_produit=<?= $valeur['id_produit'] ?>">Voir la fiche</a>
            </div>
            <!-- Fin vignette produit -->
        <?php endforeach ?>

    </div>
<?php
//  Appel du footer
require_once('inc/footer.inc.php');
?>