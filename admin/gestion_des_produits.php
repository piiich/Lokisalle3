<?php
// Important !!!!
require_once('../inc/init.inc.php');
// Tout les traitements php

// Redirection si pas admin
//if (!userAdmin()) {
//    header('location:../connexion.php');
//}

if ($_POST) {

    if (isset($_GET['action']) && $_GET['action'] == 'modifier') {
        $resultat = $pdo->prepare("REPLACE INTO produit (id_salle, date_arrivee, date_depart, prix, etat) VALUES(:id_salle, :date_arrivee, :date_depart, :prix, :etat)");

        $resultat->bindParam(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
    } else {
        $resultat = $pdo->prepare("INSERT INTO produit (id_salle, date_arrivee, date_depart, prix, etat) VALUES(:id_salle, :date_arrivee, :date_depart, :prix, :etat)");
    } // !!!!!!!!!!!!! FERMETURE DU ELSE !!!!!!!!!!!!!!

    // STR
    $resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_INT);
    $resultat -> bindParam(':date_arrivee', $_POST['date_arrivee'], PDO::PARAM_STR);
    $resultat -> bindParam(':date_depart', $_POST['date_depart'], PDO::PARAM_STR);
    $resultat -> bindParam(':prix', $_POST['prix'], PDO::PARAM_INT);
    $resultat -> bindParam(':etat', $_POST['etat'], PDO::PARAM_STR);

    if ($resultat -> execute()) {
        $_GET['action'] = 'affichage';
        $last_id = $pdo -> lastInsertId();
        $msg .='<div class="validation">Le produit N°' . $last_id . ' a bien été enregistré</div>';
    } //Pourquoi execute() dans le if ?
    // Après avoir executé ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection ...). Le problême c'est que ces traitements ce lanceront quoi qu'il arrive, même si la requête échoue.
    // En efféctuant ces traitements dans un if($resultat -> execute()) cela signifie qu'ils ne s'executerons qu'en cas de succés de la requête.

}

// Supprimer un produit
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    // Si une action de supprmier est passée dans l'URL, on vérifie qu'il y a bien un ID est que cette ID est une valeur numerique.
    if (isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])) {
        //Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du produit, je dois récupérer le nom de la photo dans la BDD. D'où la requête de selection ci-dessous
        $resultat = $pdo -> prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        $resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
        $resultat -> execute();
        if ($resultat -> rowCount() > 0) {
            $resultat = $pdo -> exec("DELETE FROM produit WHERE id_produit = $produit[id_produit]");

            if ($resultat != FALSE) {
                $_GET['action'] = 'affichage';
                $msg .='<div class="validation">Le produit N°' . $produit['id_produit'] . ' a bien été supprimé</div>';
            }
        }
    }
}

// Récupérer toutes les infos de tous les produits
// Afficher toutes les infos de tous les produits
if (isset($_GET['action']) && $_GET['action'] == 'affichage') { //Si une action pour afficher les produits est demandée dans l'URL. Alors on récupère toutes les infos de touss les produits.
    $resultat = $pdo -> query("SELECT * FROM produit");

// On affiche ces infos via des boucles, dans un tableau HTML(stocké dans une variable contenu)
    $contenu .= "<table border='1' cellpadding='10' style='text-align : center'>";
    $contenu .= "<tr>";
    for ($i=0; $i < $resultat -> columnCount() ; $i++) {
        $meta = $resultat -> getColumnMeta($i);
        $contenu .= "<th>" . $meta['name'] . "</th>";
    }
    $contenu .= '<th colspan="2">Actions</th>';
    $contenu .= "</tr>";

    while ($produit = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table prouduit. Et à chaque enregistrement toutes les infos dans un array $produit.
        $contenu .= "<tr>";//Je crée une ligne par produit
        foreach ($produit as $indice => $valeur) {//Pour chaque produit (représenté par l'array produit) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            $contenu .= "<td>" . $valeur . "</td>";

        }
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_produit=' . $produit['id_produit'] .'"><img src="' . RACINE_SITE . 'img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_produit=' . $produit['id_produit'] .'"><img src="' . RACINE_SITE . 'img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque produits
    }
    $contenu .= "</table>";
}


$page = 'Gestion Produits';
//  Appel du header
require_once('../inc/header.inc.php');
?>
    <!-- Mon contenu HTML -->
    <h1>Gestion de produits</h1>
    <ul>
        <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les produits et affichage du formulaire d'ajout de produit -->
        <li><a href="?action=affichage">Afficher les produits</a></li>
        <li><a href="?action=ajout">Ajouter un produit</a></li>
    </ul>
    <br>
    <hr>
    <br>

    <!-- Affichage du formulaire (pour ajouter ou pour modifier) -->
<?php
if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) :
// Si une action d'ajout est demandée via l'URL, dans ce cas on affiche le formulaire si dessous?>
    <?php
    if (isset($_GET['id_produit']) && is_numeric($_GET['id_produit'])) { // Dans le cas ou l'action est de modifier un produit, alors j'ai un ID dans l'URL qui va me premettre de récupèrer les infos du produit à mmodifier. (requete ci-dessous)
        $resultat = $pdo -> prepare('SELECT * FROM produit WHERE id_produit = :id_produit');
        $resultat -> bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
        if ($resultat -> execute()) {
            $produit_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
            // $produit_actuel est un array qui contient toutes les infos du produit à modifier.
        }
    }
// Si produit actuel existe (je suis dans le cadre d'une modif) alors je stock les valeurs des produits dans des variables (plus simples pour les afficher dasn les champs) sinon je stocke une valeur vide.
// Les lignes si dessous servent simplement d'éviter de mettre trop de PHP dans notre formulaire.
    $id_salle = (isset($produit_actuel)) ? $produit_actuel['id_salle'] : '';
    $date_arrivee = (isset($produit_actuel)) ? $produit_actuel['date_arrivee'] : '';
    $date_depart = (isset($produit_actuel)) ? $produit_actuel['date_depart'] : '';
    $etat = (isset($produit_actuel)) ? $produit_actuel['etat'] : '';
    $prix = (isset($produit_actuel)) ? $produit_actuel['prix'] : '';

    $action = (isset($produit_actuel)) ? 'Modifier' : 'Ajouter';
    $id_produit = (isset($produit_actuel)) ? $produit_actuel['id_produit'] : '';

    ?>
    <h2 style="text-align: center;">Ajouter un produit</h2>
    <div class="formulaire">
        <form method="post" action="" enctype="multipart/form-data" class="formulaire_modif">
            <label>Id salle : </label><br>
            <input type="text" name="id_salle" value="<?= $id_salle ?>"><br><br>

            <label>Date d'arrivée : </label><br>
            <input type="text" name="date_arrivee" value=""><br><br>

            <label>Date de départ : </label><br>
            <input type="text" name="date_depart" value="<?= $date_depart ?>"><br><br>

            <label>Prix : </label><br>
            <input type="text" name="prix" value="<?= $prix ?>"><br><br>

            <label>Etat : </label><br>
            <select name="etat">
                <option value="libre" <?= ($etat == 'libre') ? 'selected' : '' ?>>Libre</option>
                <option value="reservation" <?= ($etat == 'reservation') ? 'selected' : '' ?>>Réservation</option>
            </select><br><br>

            <input type="submit" name="<?= $action ?>">
        </form>
    </div>
<?php endif; ?>
<?= $msg ?>
<?= $contenu ?>
<?php
//  Appel du footer
require_once('../inc/footer.inc.php');
?>
