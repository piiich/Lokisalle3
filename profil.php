<?php  

require_once('inc/init.inc.php');

if(isset($_GET['action']) && $_GET['action'] == 'supprimer'){
	$id_membre = $_SESSION['membre']['id_membre'];
	$resultat = $pdo -> exec("DELETE FROM membre WHERE id_membre = $id_membre"); 
	unset($_SESSION['membre']);
}



if(!userConnecte()){ // Si la fonction me retourne FALSE
	header('location:index.php');
}

extract($_SESSION['membre']);

$id_membre = $_SESSION['membre']['id_membre'];
$req = "
SELECT c.*, m.pseudo, m.nom, m.prenom, m.email, m.civilite, m.date_enregistrement
FROM commande c 
LEFT JOIN membre m ON m.id_membre = c.id_membre WHERE c.id_membre = $id_membre ";

$resultat = $pdo -> query($req); 

if($resultat -> rowCount() > 0){

$contenu .= '<table border="1">';
$contenu .= '<tr>';
for($i = 0; $i < $resultat -> columnCount(); $i++){
	$meta = $resultat -> getColumnMeta($i);
	$contenu .= '<th>' . $meta['name'] . '</th>';
}
$contenu .= '<th>Actions</th>';
$contenu .= '</tr>';
while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
	$contenu .= '<tr>'; 
	foreach($commandes as $indice => $valeur){
		if($indice == 'photo'){
			$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>';
		}
		else{
			$contenu .= ' <td>' . $valeur . '</td>';
		}
	}
	$contenu .= '<td><a href="?action=voir&id_commande='. $commandes['id_commande'] .'"><img src="' . RACINE_SITE . 'img/eye.png" width="25"/></a></td>';
	$contenu .= '</tr>';
}
$contenu .= '</table>';
}
else{
	$contenu .= '<p>Vous n\'avez jamais commandé sur le site</p>';
}


if(isset($_GET['action']) && $_GET['action'] == 'voir'){
	if(isset($_GET['id_commande']) && !empty($_GET['id_commande']) && is_numeric($_GET['id_commande'])){
		
		
		$resultat = $pdo -> prepare("
		SELECT p.photo, p.titre, dc.* 
		FROM details_commande dc
		LEFT JOIN produit p ON p.id_produit = dc.id_produit
		WHERE dc.id_commande = :id");	
		
		$resultat -> bindParam(':id', $_GET['id_commande'], PDO::PARAM_INT);
		$resultat -> execute();
		
		if($resultat -> rowCount() > 0){
			$contenu .= '<hr/><h2>Détails de la commande N°' . $_GET['id_commande'] . '</h2>';
			$contenu .= '<table border="1">';
			$contenu .= '<tr>';
			for($i = 0; $i < $resultat -> columnCount(); $i++){
				$meta = $resultat -> getColumnMeta($i);
				$contenu .= '<th>' . $meta['name'] . '</th>';
			}
			$contenu .= '</tr>';
			while($commandes = $resultat -> fetch(PDO::FETCH_ASSOC)){ 
				$contenu .= '<tr>'; 
				foreach($commandes as $indice => $valeur){
					if($indice == 'photo'){
						$contenu .= '<td><img src="' . RACINE_SITE . 'photo/' . $valeur . '" height="100"/></td>';
					}
					else{
						$contenu .= ' <td>' . $valeur . '</td>';
					}
				}
				$contenu .= '</tr>';
			}
			$contenu .= '</table>';
		}	
	}	
}





$page = 'Profil';
require_once('inc/header.inc.php');
?>

<div class="container">
	<h1>Profil de <?= $pseudo ?></h1>

<div class="profil">
	<p>Bonjour <?= $pseudo?> !</p><br/>
	
	<div class="profil_infos">
		<ul>
			<li>Pseudo : <b><?= $pseudo ?></b></li>
			<li>Prénom : <b><?= $prenom ?></b></li>
			<li>Nom: <b><?= $nom ?></b></li>
			<li>Email : <b><?= $email ?></b></li>
		</ul>
	</div>
	<a href="membre.php">Modifier mon profil</a><br/>
	<a href="?action=supprimer">Supprimer mon compte</a><br/>
</div>

<hr/><h2>Suivi des commandes</h2>
<?= $contenu ?>


	
</div>
<?php 
require_once('inc/footer.inc.php');