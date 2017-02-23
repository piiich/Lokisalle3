<?php 
require_once('../inc/init.inc.php');

//// Redirection si pas admin
//if (!userAdmin()) {
//    header('location:../connexion.php');
//}

// Récupérer toutes les infos de tous les commandes
// Afficher toutes les infos de tous les commandes
if (isset($_GET['action']) && $_GET['action'] == 'note') { //Si une action pour afficher les commandes est demandée dans l'URL. Alors on récupère toutes les infos de touss les commandes.
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

    while ($avis = $resultat -> fetch(PDO::FETCH_ASSOC)) {//La boucle while parcourt tous les enregistrements de notre table prouduit. Et à chaque enregistrement toutes les infos dans un array $commande.
        $contenu .= "<tr>";//Je crée une ligne par commande
        foreach ($avis as $indice => $valeur) {//Pour chaque commande (représenté par l'array commande) j'écris chaque info dans une cellule <td>. La boucle foreach me permet de le faire dynamiquement.
            $contenu .= "<td>" . $valeur . "</td>";

        }
        debug($avis);
        // En face de chaque enregistrements on ajoute deux actions : Modifier et supprimer en GET en précisant l4ID de chaque enregistrement.
        $contenu .= '<td><a href="?action=modifier&id_avis=' . $avis['id_avis'] .'"><img src="' . RACINE_SITE . 'img/edit.png"</a></td>';
        $contenu .= '<td><a href="?action=supprimer&id_avis=' . $avis['id_avis'] .'"><img src="' . RACINE_SITE . 'img/delete.png"</a></td>';
        $contenu .= "</tr>";//Je ferme la ligne une fois j'ai parcouru chaque commandes
    }
    $contenu .= "</table>";
}


$page = 'Statistique';
require_once('../inc/header.inc.php');
 ?>

    <h1>Statistiques</h1>
    <ul>
        <!-- Les deux liens si dessous (sous-menu) permettent de lancer 2 actions : affichage de tous les commandes et affichage du formulaire d'ajout de commande -->
        <li><a href="?action=note">Top 5 des salles les mieux notées </a></li>
        <li><a href="?action=commande">Top 5 des salles les plus commandées</a></li>
        <li><a href="?action=quantite">Top 5 des membres qui achètent le plus (en termes de quantité)</a></li>
        <li><a href="?action=prix">Top 5 des membres qui achètent le plus cher (en termes de prix)</a></li>
    </ul>
    <br>
    <hr>
    <br>


<?= $contenu ?>

 <?php 
require_once('../inc/footer.inc.php');
  ?>