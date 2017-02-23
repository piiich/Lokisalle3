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
        $resultat = $pdo -> prepare("UPDATE commande SET id_membre=:id_membre, id_produit=:id_produit, date_enregistrement=:date_enregistrement WHERE id_commande=:id_commande");
        $resultat -> bindParam(':id_commande', $_POST['id_commande'], PDO::PARAM_INT);

    }
    else{
        $resultat = $pdo -> prepare("INSERT INTO commande (id_membre, id_produit, date_enregistrement) VALUES(:id_membre, :id_produit, :date_enregistrement)");
    } // !!!!!!!!!!!!! FERMETURE DU ELSE !!!!!!!!!!!!!!


    $resultat -> bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_INT);
    $resultat -> bindParam(':id_produit', $_POST['id_produit'], PDO::PARAM_INT);
    $resultat -> bindParam(':date_enregistrement', $_POST['date_enregistrement'], PDO::PARAM_STR);


    if ($resultat -> execute()) {
        $_GET['action'] = 'affichage';
        $last_id = $pdo -> lastInsertId();
        $msg .='<div class="validation">La commande N°' . $last_id . ' a bien été enregistré</div>';
    } //Pourquoi execute() dans le if ?
    // Après avoir executé ma requête, je souhaite lancer d'autres traitements (affichage d'un message, redirection ...). Le problême c'est que ces traitements ce lanceront quoi qu'il arrive, même si la requête échoue.
    // En efféctuant ces traitements dans un if($resultat -> execute()) cela signifie qu'ils ne s'executerons qu'en cas de succés de la requête.

}

// Supprimer un commande
if (isset($_GET['action']) && $_GET['action'] == 'supprimer') {
    // Si une action de supprmier est passée dans l'URL, on vérifie qu'il y a bien un ID est que cette ID est une valeur numerique.
    if (isset($_GET['id_commande']) && is_numeric($_GET['id_commande'])) {
        //Si c'est OK au niveau de l'ID, puis que je dois supprimer la photo du commande, je dois récupérer le nom de la photo dans la BDD. D'où la requête de selection ci-dessous
        $resultat = $pdo -> prepare("SELECT * FROM commande WHERE id_commande = :id_commande");
        $resultat -> bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
        $resultat -> execute();
        if ($resultat -> rowCount() > 0) {
            $resultat = $pdo -> exec("DELETE FROM commande WHERE id_commande = $commande[id_commande]");

            if ($resultat != FALSE) {
                $_GET['action'] = 'affichage';
                $msg .='<div class="validation">Le commande N°' . $commande['id_commande'] . ' a bien été supprimé</div>';
            }
        }
    }
}

// Récupérer toutes les infos de tous les commandes
// Afficher toutes les infos de tous les commandes
if (isset($_GET['action']) && $_GET['action'] == 'affichage') { //Si une action pour afficher les commandes est demandée dans l'URL. Alors on récupère toutes les infos de touss les commandes.
    $resultat = $pdo -> query("SELECT * FROM commande");

// On affiche ces infos via des boucles, dans un tableau HTML(stocké dans une variable contenu)
    $contenu .= "<table border='1' cellpadding='10' style='text-align : center'>";
    $contenu .= "<tr>";
    for ($i=0; $i < $resultat -> columnCount() ; $i++) {
        $meta = $resultat -> getColumnMeta($i);
        $contenu .= "<th>" . $meta['name'] . "</th>";
    }
    $contenu .= '<th colspan="2">Actions</th>';
    $contenu .= "</tr>";

    while ($commande = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table prouduit. Et à chaque enregistrement toutes les infos dans un array $commande.
        $contenu .= "<tr>";//Je crée une ligne par commande
        foreach ($commande as $indice => $valeur) {//Pour chaque commande (représenté par l'array commande) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            $contenu .= "<td>" . $valeur . "</td>";

        }
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_commande=' . $commande['id_commande'] .'"><img src="' . RACINE_SITE . 'img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_commande=' . $commande['id_commande'] .'"><img src="' . RACINE_SITE . 'img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque commandes
    }
    $contenu .= "</table>";
}


$page = 'Gestion Commandes';
//  Appel du header
require_once('../inc/header.inc.php');
?>
    <!-- Mon contenu HTML -->
    <h1>Gestion de commandes</h1>
    <ul>
        <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les commandes et affichage du formulaire d'ajout de commande -->
        <li><a href="?action=affichage">Afficher les commandes</a></li>
        <li><a href="?action=ajout">Ajouter un commande</a></li>
    </ul>
    <br>
    <hr>
    <br>

    <!-- Affichage du formulaire (pour ajouter ou pour modifier) -->
<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modifier')) :
// Si une action d'ajout est demandée via l'URL, dans ce cas on affiche le formulaire si dessous?>
    <?php
    if (isset($_GET['id_commande']) && is_numeric($_GET['id_commande'])) { // Dans le cas ou l'action est de modifier un commande, alors j'ai un ID dans l'URL qui va me premettre de récupèrer les infos du commande à mmodifier. (requete ci-dessous)
        $resultat = $pdo -> prepare('SELECT * FROM commande WHERE id_commande = :id_commande');
        $resultat -> bindParam(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
        if ($resultat -> execute()) {
            $commande_actuel = $resultat -> fetch(PDO::FETCH_ASSOC);
            // $commande_actuel est un array qui contient toutes les infos du commande à modifier.
        }
    }
// Si commande actuel existe (je suis dans le cadre d'une modif) alors je stock les valeurs des commandes dans des variables (plus simples pour les afficher dasn les champs) sinon je stocke une valeur vide.
// Les lignes si dessous servent simplement d'éviter de mettre trop de PHP dans notre formulaire.
    $id_membre = (isset($commande_actuel)) ? $commande_actuel['id_membre'] : '';
    $id_produit = (isset($commande_actuel)) ? $commande_actuel['id_produit'] : '';
    $date_enregistrement = (isset($commande_actuel)) ? $commande_actuel['date_enregistrement'] : '';

    $action = (isset($commande_actuel)) ? 'Modifier' : 'Ajouter';
    $id_commande = (isset($commande_actuel)) ? $commande_actuel['id_commande'] : '';

    ?>

    <h2 style="text-align: center;">Ajouter une commande</h2>
    <div class="formulaire">
        <form method="post" action="" enctype="multipart/form-data" class="formulaire_modif">
            <input type="hidden" name="id_commande" value="<?= $id_commande ?>">
            <label>Id membre : </label><br>

            <select name="id_membre" value="<?= $id_membre ?>">

                <?php
                $resultat = $pdo->query('SELECT * FROM membre');
                while ($membre = $resultat->fetch()){

                    ?>
                    <option value="<?= $membre['id_membre']; ?>"><?= $membre['id_membre']; ?></option>
                    <?php
                }
                ?>
            </select><br><br>
            <label>Id produit : </label><br>

            <select name="id_produit" value="<?= $id_produit ?>">

                <?php
                $resultat = $pdo->query('SELECT * FROM produit');
                while ($produit = $resultat->fetch()){

                    ?>
                    <option value="<?= $produit['id_produit']; ?>"><?= $produit['id_produit']; ?></option>
                    <?php
                }
                ?>
            </select><br><br>
<!--            <p>Date: <input type="text" id="datepicker"></p>-->


            <label>Date d'enregistrement : </label><br>
            <input type="text" name="date_enregistrement" value="<?= $date_enregistrement ?>"><br><br>
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
<!--<script>-->
<!--    $( function() {-->
<!--        $( "#datepicker" ).datepicker();-->
<!--    } );-->
<!--</script>-->