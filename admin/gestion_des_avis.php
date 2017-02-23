<?php
// Important !!!!
require_once('../inc/init.inc.php');
// Tout les traitements php

//// Redirection si pas admin
//if (!userAdmin()) {
//    header('location:../connexion.php');
//}

if ($_POST) {

    if (isset($_GET['action']) && $_GET['action'] == 'modifier') {
        $resultat = $pdo->prepare("REPLACE INTO avis (id_membre, id_salle, commentaire, note) VALUES(:id_membre, :commentaire,  :note)");

        $resultat->bindParam(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
    } else {
        $resultat = $pdo->prepare("INSERT INTO avis (id_membre, id_salle, commentaire, note) VALUES(:id_membre, :commentaire,  :note)");
    } // !!!!!!!!!!!!! FERMETURE DU ELSE !!!!!!!!!!!!!!


    // STR
    $resultat -> bindParam(':id_membre', $_POST['id_membre'], PDO::PARAM_STR);
    $resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_STR);
    $resultat -> bindParam(':commentaire', $_POST['commentaire'], PDO::PARAM_STR);
    $resultat -> bindParam(':note', $_POST['note'], PDO::PARAM_STR);


    if ($resultat -> execute()) {
        $_GET['action'] = 'affichage';
        $last_id = $pdo -> lastInsertId();
        $msg .='<div class="validation">Le commentaire N°' . $last_id . ' a bien été enregistré</div>';
    } //Pourquoi execute() dans le if ?
    // Après avoir executé ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection ...). Le problême c'est que ces traitements ce lanceront quoi qu'il arrive, même si la requête échoue.
    // En efféctuant ces traitements dans un if($resultat -> execute()) cela signifie qu'ils ne s'executerons qu'en cas de succés de la requête.

}

// Supprimer un avis
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    // Si une action de supprimer est passée dans l'URL, on vérifie qu'il y a bien un ID est que cette ID est une valeur numerique.
    if (isset($_GET['id_avis']) && is_numeric($_GET['id_avis'])) {
        //Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du avis, je dois récupérer le nom de la photo dans la BDD. D'où la requête de selection ci-dessous
        $resultat = $pdo -> prepare("SELECT * FROM avis WHERE id_avis = :id_avis");
        $resultat -> bindParam(':id_avis', $_GET['id_avis'], PDO::PARAM_INT);
        $resultat -> execute();
        if ($resultat -> rowCount() > 0) {
            $resultat = $pdo -> exec("DELETE FROM avis WHERE id_avis = $avis[id_avis]");

            if ($resultat != FALSE) {
                $_GET['action'] = 'affichage';
                $msg .='<div class="validation">Le avis N°' . $avis['id_avis'] . ' a bien été supprimé</div>';
            }
        }
    }
}
// Récupérer toutes les infos de tous les avis
// Afficher toutes les infos de tous les avis
if (isset($_GET['action']) && $_GET['action'] == 'affichage') { //Si une action pour afficher les avis est demandée dans l'URL. Alors on récupère toutes les infos de touss les avis.
    $resultat = $pdo -> query("SELECT * FROM avis");

// On affiche ces infos via des boucles, dans un tableau HTML(stocké dans une variable contenu)
    $contenu .= "<table border='1' cellpadding='10' style='text-align : center'>";
    $contenu .= "<tr>";
    for ($i=0; $i < $resultat -> columnCount() ; $i++) {
        $meta = $resultat -> getColumnMeta($i);
        $contenu .= "<th>" . $meta['name'] . "</th>";
    }
    $contenu .= '<th colspan="2">Actions</th>';
    $contenu .= "</tr>";

    while ($avis = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table prouduit. Et à chaque enregistrement toutes les infos dans un array $avis.
        $contenu .= "<tr>";//Je crée une ligne par avis
        foreach ($avis as $indice => $valeur) {//Pour chaque avis (représenté par l'array avis) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            $contenu .= "<td>" . $valeur . "</td>";

        }
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_avis=' . $avis['id_avis'] .'"><img src="' . RACINE_SITE . '../img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_avis=' . $avis['id_avis'] .'"><img src="' . RACINE_SITE . '../img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque avis
    }
    $contenu .= "</table>";
}

$page = 'Gestion Membres';
//  Appel du header
require_once('../inc/header.inc.php');
?>
<!-- Mon contenu HTML -->
<h1>Gestion des avis</h1>
<ul>
    <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les membres et affichage du formulaire d'ajout de membre -->
    <li><a href="?action=affichage">Afficher les avis</a></li>
    <li><a href="?action=ajout">Ajouter un avis</a></li>
</ul>
<br>
<hr>
<br>


<!-- Affichage du formulaire (pour ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) :
// Si une action d'ajout est demandée via l'URL, dans ce cas on affiche le formulaire si dessous?>
    <?php
    if (isset($_GET['id_avis']) && is_numeric($_GET['id_avis'])) { // Dans le cas ou l'action est de modifier un avis, alors j'ai un ID dans l'URL qui va me premettre de récupèrer les infos du avis à mmodifier. (requete ci-dessous)
        $resultat = $pdo -> prepare('SELECT * FROM avis WHERE id_avis = :id_avis');
        $resultat -> bindParam(':id_avis', $_GET['id_avis'], PDO::PARAM_INT);
        if ($resultat -> execute()) {
            $avis_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
            // $avis_actuel est un array qui contient toutes les infos du avis à modifier.
        }
    }
// Si avis actuel existe (je suis dans le cadre d'une modif) alors je stock les valeurs des aviss dans des variables (plus simples pour les afficher dasn les champs) sinon je stocke une valeur vide.
// Les lignes si dessous servent simplement d'éviter de mettre trop de PHP dans notre formulaire.
    $id_membre = (isset($avis_actuel)) ? $avis_actuel['id_membre'] : '';
    $commentaire = (isset($avis_actuel)) ? $avis_actuel['commentaire'] : '';
    $note = (isset($avis_actuel)) ? $avis_actuel['note'] : '';
    $id_salle = (isset($avis_actuel)) ? $avis_actuel['salle'] : '';

    $action = (isset($avis_actuel)) ? 'Modifier' : 'Ajouter';
    $id_avis = (isset($avis_actuel)) ? $avis_actuel['id_avis'] : '';

    ?>

    <h2 style="text-align: center;">Ajouter un avis</h2>
    <div class="formulaire">
        <form method="post" action="" enctype="multipart/form-data" class="formulaire_modif">
            <label>ID membre : </label><br>
            <input type="text" name="id_membre" value="<?= $id_membre ?>"><br><br>

            <label>ID salle : </label><br>
            <input type="text" name="id_salle" value="<?= $id_salle ?>"><br><br>

            <label>Commentaire : </label><br>
            <textarea name="commentaire" id="" cols="30" rows="10"><?= $commentaire ?></textarea><br><br>

            <label>Note : </label><br>
            <input type="text" name="note" value="<?= $note ?>"><br><br>

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

