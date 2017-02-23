<?php
// Important !!!!
require_once('../inc/init.inc.php');
// Tout les traitements php

// Ajouter et modifier un salle
// Dans ces traitements on va à la fois être capable d'ajouter un nouveau salle et à la fois ajouter un salle existant.


if ($_POST) {

    $nom_photo = 'default.jpg';

    // $nom_photo va quoi qu'il arrive contenir le nom de la photo a enregistrer en BDD.
    // Elle va soit contenir un nom part defaut, soit le nom de l'image uploadée (on modifira le nom) soit le nom de la photo en cours de modification
    // Dans le cas où une nouvelle photo est ajoutée, en plus de renommer cette photo (pour éviter les collisions) je l'enregistre dans le serveur (fonction copy())
    if (isset($_POST['photo_actuelle'])) {
        $nom_photo = $_POST['photo_actuelle'];
    }

    if (!empty($_FILES['photo']['name'])) {
        // On renome la photots pour éviter les (doublons sur notre serveur)
        $nom_photo = $_POST['titre'] . '_' . $_FILES['photo']['name'];

        // Enregistrement de le photo sur le serveur.
        $chemin_photo = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $nom_photo;
        // $chemin_photo est l'emplacement définitif de la photo depuis le base du serveur jusqu'au nom du fichier.

        copy($_FILES['photo']['tmp_name'], $chemin_photo); // On déplace la photo depuis sont emplacement temporaire, vers son emplacement définitif. Emplacement temporaire : $_FILES['photo']['tmp_name']
    }
    // Enregistrement dans la BDD
    // Depuis SQL 5.7, dans une requette REPLACE on ne peut peux plus mettre la clé primaire NULL ou vide. On doit donc faire une requête pour l'ajout et une requête pour la modif. D'où le if/else ci-dessous.
    if (isset($_GET['action']) && $_GET['action'] == 'modifier') {
        $resultat = $pdo -> prepare("UPDATE salle SET titre=:titre, description=:description, pays=:pays, ville=:ville, adresse=:adresse, cp=:cp, capacite=:capacite, photo=$nom_photo, categorie=:categorie WHERE id_salle=:id_salle");

        $resultat -> bindParam(':id_salle', $_POST['id_salle'], PDO::PARAM_INT);
    }
    else{
        $resultat = $pdo -> prepare("INSERT INTO salle (titre, description, pays, ville, adresse, cp, capacite, photo, categorie) VALUES(:titre, :description, :titre, :ville, :adresse, :cp, :capacite, '$nom_photo', :categorie)");
    } // !!!!!!!!!!!!! FERMETURE DU ELSE !!!!!!!!!!!!!!



    // STR
    $resultat -> bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
    $resultat -> bindParam(':description', $_POST['description'], PDO::PARAM_STR);
    $resultat -> bindParam(':pays', $_POST['pays'], PDO::PARAM_STR);
    $resultat -> bindParam(':ville', $_POST['ville'], PDO::PARAM_STR);
    $resultat -> bindParam(':adresse', $_POST['adresse'], PDO::PARAM_STR);
    $resultat -> bindParam(':cp', $_POST['cp'], PDO::PARAM_STR);
    $resultat -> bindParam(':capacite', $_POST['capacite'], PDO::PARAM_STR);
    $resultat -> bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);


    if ($resultat -> execute()) {
        $_GET['action'] = 'affichage';
        $last_id = $pdo -> lastInsertId();
        $msg .='<div class="validation">La salle N°' . $last_id . ' a bien été enregistré</div>';
    } //Pourquoi execute() dans le if ?
    // Après avoir executé ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection ...). Le problême c'est que ces traitements ce lanceront quoi qu'il arrive, même si la requête échoue.
    // En efféctuant ces traitements dans un if($resultat -> execute()) cela signifie qu'ils ne s'executerons qu'en cas de succés de la requête.
}

// Supprimer une salle
// Supprimer du serveur la photo correspondant au salle.
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    // Si une action de supprmier est passée dans l'URL, on vérifie qu'il y a bien un ID est que cette ID est uhne valeur numerique.
    if (isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])) {
        //Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du salle, je dois récupérer le nom de la photo dans la BDD. D'où la requête de selection ci-dessous
        $resultat = $pdo -> prepare("SELECT * FROM salle WHERE id_salle = :id_salle");
        $resultat -> bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_INT);
        $resultat -> execute();
        if ($resultat -> rowCount() > 0) {
            //Si on a trouvé au moins un salle existante dans la BDD, c'est que l'Id était correcte. On vérifie cela au cas oùl'ID transmis dans l'URL aurait était modifiée ou erronée...
            $salle = $resultat -> fetch(PDO::FETCH_ASSOC);
            // Pour pouvoir supprimer une photo, il nous faut son chemin absolu que l'on reconstitue depuis la racine du serveur ci-dessous
            $chemin_de_la_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . 'photo/' . $salle['photo'];

            // Dernières verifs : Si le fichier existe et que ce n'est pas la photo par défaut, alors la fonction unlink() supprime le fichier.
            if (file_exists($chemin_de_la_photo_a_supprimer) && $salle['photo'] != 'default.jpg') {
                unlink($chemin_de_la_photo_a_supprimer); // unlink : supprime un fichier de mon serveur
            }

            // Aprés avoir supprimer la photo du salle on peut enfin supprimer la salle elle même
            $resultat = $pdo -> exec("DELETE FROM salle WHERE id_salle = $salle[id_salle]");

            if ($resultat != FALSE) {
                $_GET['action'] = 'affichage';
                $msg .='<div class="validation">La salle N°' . $salle['id_salle'] . ' a bien été supprimé</div>';
            }
        }
    }
}

// Récupérer toutes les infos de toute les salles
// Afficher toutes les infos de toute les salles
if (isset($_GET['action']) && $_GET['action'] == 'affichage') { //Si une action pour afficher les salles est demandée dans l'URL. Alors on récupère toutes les infos de tous les salles.
    $resultat = $pdo -> query("SELECT * FROM salle");

// On affiche ces infos via des boucles, dans un tableau HTMLM(stocké dans une variable contenu)
    $contenu .= "<table border='1' cellpadding='10' style='text-align : center'>";
    $contenu .= "<tr>";
    for ($i=0; $i < $resultat -> columnCount() ; $i++) {
        $meta = $resultat -> getColumnMeta($i);
        $contenu .= "<th>" . $meta['name'] . "</th>";
    }
    $contenu .= '<th colspan="2">Actions</th>';
    $contenu .= "</tr>";

    while ($salle = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table salle. Et à chaque enregistrement toutes les infos dans un array $salle.
        $contenu .= "<tr>";//Je crée une ligne par salle
        foreach ($salle as $indice => $valeur) {//Pour chaque salle (représenté par l'array salle) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            // Lorsqu'on parcourt un enregistrement on souhaite afficher la photo dans une balise image et non en texte. On fait donc une condition dans le for each :
            if ($indice == 'photo') {
                $contenu .= '<td><img src="' . RACINE_SITE. '/photo/' . $valeur . '.jpg" height="100"/></td>';
            }
            else{
                $contenu .= "<td>" . $valeur . "</td>";
            }
        }
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_salle=' . $salle['id_salle'] .'"><img src="' . RACINE_SITE . 'img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_salle=' . $salle['id_salle'] .'"><img src="' . RACINE_SITE . 'img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque salles
    }
    $contenu .= "</table>";
}
$page = 'Gestion Boutique';
require_once ('../inc/header.inc.php');
?>

<h1>Gestion des salles</h1>
<ul>
    <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les produits et affichage du formulaire d'ajout de produit -->
    <li><a href="?action=affichage">Afficher les salles</a></li>
    <li><a href="?action=ajout">Ajouter une salle</a></li>
</ul>
<br>
<hr>
<br>

    <!-- Affichage du formulaire (pour ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) :
// Si une action d'jout est demandée via l'URL, dans ce cas on affiche le formulaire si dessous?>
    <?php
if (isset($_GET['id_salle']) && is_numeric($_GET['id_salle'])) { // Dans le cas ou l'action est de momdifier un salle, alors j'ai un ID dans l'URL qui va me premettre de récupèrer les infos du salle à mmodifier. (requete ci-dessous)
    $resultat = $pdo -> prepare('SELECT * FROM salle WHERE id_salle = :id_salle');
    $resultat -> bindParam(':id_salle', $_GET['id_salle'], PDO::PARAM_INT);
    if ($resultat -> execute()) {
        $salle_actuelle = $resultat -> fetch(PDO::FETCH_ASSOC);
        // $salle_actuelle est un array qui contient toutes les infos du salle à modifier.
    }
}
// Si produit actuel existe (je suis dans le cadre d'une modif) alors je stock les valeurs des produits dans des variables (plus simples pour les afficher dasn les champs) sinon je stocke une valeur vide.
// Les lignes si dessous servent simplement d'éviter de mettre trop de PHP dans notre formulaire.
$id_salle = (isset($salle_actuelle)) ? $salle_actuelle['id_salle'] : '';
$titre = (isset($salle_actuelle)) ? $salle_actuelle['titre'] : '';
$description = (isset($salle_actuelle)) ? $salle_actuelle['description'] : '';
$photo = (isset($salle_actuelle)) ? $salle_actuelle['photo'] : '';
$pays = (isset($salle_actuelle)) ? $salle_actuelle['pays'] : '';
$ville = (isset($salle_actuelle)) ? $salle_actuelle['ville'] : '';
$adresse = (isset($salle_actuelle)) ? $salle_actuelle['adresse'] : '';
$cp = (isset($salle_actuelle)) ? $salle_actuelle['cp'] : '';
$capacite = (isset($salle_actuelle)) ? $salle_actuelle['capacite'] : '';
$categorie = (isset($salle_actuelle)) ? $salle_actuelle['categorie'] : '';

$action = (isset($salle_actuelle)) ? 'Modifier' : 'Ajouter';

?>

<h2 style="text-align: center;">Ajouter une salle</h2>
<div class="formulaire">
    <form method="post" action="" enctype="multipart/form-data" class="formulaire_modif">
        <!-- L'attribut enctype permet de gérer les fichiers uploader et de traiter grâce à la super globale $_FILES-->
        <input type="hidden" name="id_salle" value="<?= $id_salle ?>">

        <label>Titre : </label><br>
        <input type="text" name="titre" value="<?= $titre ?>"><br><br>

        <label>Déscription : </label><br>
        <input type="text" name="description" value="<?= $description ?>"><br><br>

        <label>Pays : </label><br>
        <input type="text" name="pays" value="<?= $pays ?>"><br><br>

        <label>Ville : </label><br>
        <input type="text" name="ville" value="<?= $ville ?>"><br><br>

        <label>Adresse : </label><br>
        <input type="text" name="adresse" value="<?= $adresse ?>"><br><br>

        <label>Code postal : </label><br>
        <input type="text" name="cp" value="<?= $cp ?>"><br><br>

        <label>Capacité : </label><br>
        <input type="text" name="capacite" value="<?= $capacite ?>"><br><br>

        <label>Catégorie : </label><br>
        <select name="categorie">
            <option>--Selectionnez--</option>
            <option <?= ($categorie =='reunion') ? 'selected' : ''?> value="reunion">Réunion</option>
            <option <?= ($categorie =='bureau') ? 'selected' : ''?> value="bureau">Bureau</option>
            <option <?= ($categorie =='formation') ? 'selected' : ''?> value="formation">Formation</option>
        </select>

        <?php if (isset($salle_actuelle)) : ?>
            <input type="hidden" name="photo_actuelle" value="<?= $photo ?>"><br><br>
            <img src="<?= RACINE_SITE ?>photo/<?= $photo ?>" width="100">
        <?php endif; ?>

        <input type="submit" value="Enregistrer" name="">
    </form>
</div>
<?php endif; ?>
<?= $msg ?>
<?= $contenu ?>
<?php
//  Appel du footer
require_once('../inc/footer.inc.php');
?>
