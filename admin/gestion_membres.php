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
        $resultat = $pdo->prepare("UPDATE membre SET pseudo=:pseudo, mdp=:mdp, nom=:nom, prenom=:prenom, email=:email, civilite=:civilite, statut=:statut WHERE id_membre=:id_membre");

        $resultat->bindParam(':id_membre', $_POST['id_membre'], PDO::PARAM_INT);
    } else {
        $resultat = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut) VALUES(:pseudo, :mdp, :nom, :prenom, :email, :civilite, :statut)");
    } // !!!!!!!!!!!!! FERMETURE DU ELSE !!!!!!!!!!!!!!


    // STR
    $resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $resultat -> bindParam(':mdp', $_POST['mdp'], PDO::PARAM_STR);
    $resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
    $resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
    $resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
    $resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);

    // INT
    $resultat -> bindParam(':statut', $_POST['statut'], PDO::PARAM_INT);

    if ($resultat -> execute()) {
        $_GET['action'] = 'affichage';
        $last_id = $pdo -> lastInsertId();
        $msg .='<div class="validation">Le membre N°' . $last_id . ' a bien été enregistré</div>';
    } //Pourquoi execute() dans le if ?
    // Après avoir executé ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection ...). Le problême c'est que ces traitements ce lanceront quoi qu'il arrive, même si la requête échoue.
    // En efféctuant ces traitements dans un if($resultat -> execute()) cela signifie qu'ils ne s'executerons qu'en cas de succés de la requête.

}

// Supprimer un membre
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    // Si une action de supprimer est passée dans l'URL, on vérifie qu'il y a bien un ID est que cette ID est une valeur numerique.
    if (isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])) {
        //Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du membre, je dois récupérer le nom de la photo dans la BDD. D'où la requête de selection ci-dessous
        $resultat = $pdo -> prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
        $resultat -> bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
        $resultat -> execute();
        if ($resultat -> rowCount() > 0) {
            $resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $membre[id_membre]");

            if ($resultat != FALSE) {
                $_GET['action'] = 'affichage';
                $msg .='<div class="validation">Le membre N°' . $membre['id_membre'] . ' a bien été supprimé</div>';
            }
        }
    }
}

// Récupérer toutes les infos de tous les membres
// Afficher toutes les infos de tous les membres
if (isset($_GET['action']) && $_GET['action'] == 'affichage') { //Si une action pour afficher les membres est demandée dans l'URL. Alors on récupère toutes les infos de touss les membres.
    $resultat = $pdo -> query("SELECT * FROM membre");

// On affiche ces infos via des boucles, dans un tableau HTML(stocké dans une variable contenu)
    $contenu .= "<table border='1' cellpadding='10' style='text-align : center'>";
    $contenu .= "<tr>";
    for ($i=0; $i < $resultat -> columnCount() ; $i++) {
        $meta = $resultat -> getColumnMeta($i);
        $contenu .= "<th>" . $meta['name'] . "</th>";
    }
    $contenu .= '<th colspan="2">Actions</th>';
    $contenu .= "</tr>";

    while ($membre = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table prouduit. Et à chaque enregistrement toutes les infos dans un array $membre.
        $contenu .= "<tr>";//Je crée une ligne par membre
        foreach ($membre as $indice => $valeur) {//Pour chaque membre (représenté par l'array membre) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            $contenu .= "<td>" . $valeur . "</td>";

        }
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_membre=' . $membre['id_membre'] .'"><img src="' . RACINE_SITE . 'img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_membre=' . $membre['id_membre'] .'"><img src="' . RACINE_SITE . 'img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque membres
    }
    $contenu .= "</table>";
}

$page = 'Gestion Membres';
//  Appel du header
require_once('../inc/header.inc.php');
?>
<!-- Mon contenu HTML -->
<h1>Gestion de membres</h1>
<ul>
    <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les membres et affichage du formulaire d'ajout de membre -->
    <li><a href="?action=affichage">Afficher les membres</a></li>
    <li><a href="?action=ajout">Ajouter un membre</a></li>
</ul>
<br>
<hr>
<br>


    <!-- Affichage du formulaire (pour ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) :
// Si une action d'ajout est demandée via l'URL, dans ce cas on affiche le formulaire si dessous?>
<?php
if (isset($_GET['id_membre']) && is_numeric($_GET['id_membre'])) { // Dans le cas ou l'action est de modifier un membre, alors j'ai un ID dans l'URL qui va me premettre de récupèrer les infos du membre à mmodifier. (requete ci-dessous)
    $resultat = $pdo -> prepare('SELECT * FROM membre WHERE id_membre = :id_membre');
    $resultat -> bindParam(':id_membre', $_GET['id_membre'], PDO::PARAM_INT);
    if ($resultat -> execute()) {
        $membre_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
        // $membre_actuel est un array qui contient toutes les infos du membre à modifier.
    }
}
// Si membre actuel existe (je suis dans le cadre d'une modif) alors je stock les valeurs des membres dans des variables (plus simples pour les afficher dasn les champs) sinon je stocke une valeur vide.
// Les lignes si dessous servent simplement d'éviter de mettre trop de PHP dans notre formulaire.
$pseudo = (isset($membre_actuel)) ? $membre_actuel['pseudo'] : '';
$mdp = (isset($membre_actuel)) ? $membre_actuel['mdp'] : '';
$nom = (isset($membre_actuel)) ? $membre_actuel['nom'] : '';
$prenom = (isset($membre_actuel)) ? $membre_actuel['prenom'] : '';
$email = (isset($membre_actuel)) ? $membre_actuel['email'] : '';
$civilite = (isset($membre_actuel)) ? $membre_actuel['civilite'] : '';
$statut = (isset($membre_actuel)) ? $membre_actuel['statut'] : '';

$action = (isset($membre_actuel)) ? 'Modifier' : 'Ajouter';
$id_membre = (isset($membre_actuel)) ? $membre_actuel['id_membre'] : '';

?>

<h2 style="text-align: center;">Ajouter un membre</h2>
<div class="formulaire">
    <form method="post" action="" enctype="multipart/form-data" class="formulaire_modif">
        <input type="hidden" name="id_membre" value="<?= $id_membre ?>">
        <label>Pseudo : </label><br>
        <input type="text" name="pseudo" value="<?= $pseudo ?>"><br><br>

        <label>Mot de passe : </label><br>
        <input type="password" name="mdp" value=""><br><br>

        <label>Nom : </label><br>
        <input type="text" name="nom" value="<?= $nom ?>"><br><br>

        <label>Prenom : </label><br>
        <input type="text" name="prenom" value="<?= $prenom ?>"><br><br>

        <label>Email : </label><br>
        <input type="mail" name="email" value="<?= $email ?>"><br><br>

        <label>Cvivilité : </label><br>
        <select name="civilite">
            <option value="m" <?= ($civilite == 'm') ? 'selected' : '' ?>>Homme</option>
            <option value="f" <?= ($civilite == 'f') ? 'selected' : '' ?>>Femme</option>
        </select><br><br>

        <label>Statut : </label><br>
        <select name="civilite">
            <option value="0" <?= ($statut == '0') ? 'selected' : '' ?>>Client</option>
            <option value="1" <?= ($statut == '1') ? 'selected' : '' ?>>Admin</option>
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
