<?php  

require_once('inc/init.inc.php');
// DECONNEXION

if(isset($_GET['action']) && $_GET['action'] == 'deconnexion'){
	unset($_SESSION['membre']);
	header('location:index.php');
}


// INSCRIPTION

if(isset($_POST['inscription'])){
  
  $verif_caractere = preg_match('#^[a-zA-Z0-9._-]+$#' , $_POST['pseudo']); 
  
  if(!empty($_POST['pseudo'])){
    if($verif_caractere){
      if(strlen($_POST['pseudo']) < 3 || strlen($_POST['pseudo']) > 20){
        $msg .= '<div class="erreur">Veuillez renseigner un pseudo de 3 à 20 caractères ! </div>';  
      }
    }
    else{
      $msg .= '<div class="erreur">Pseudo : Caractères acceptés : A à Z, 0 à 9 et ".", "-" et "_" </div>';
    }
  }
  else{
    $msg .= '<div class="erreur">Veuillez renseigner un pseudo !</div>';
  }
  
  if(empty($msg)){ 
    $resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $resultat-> execute(); 
    
    if($resultat -> rowCount() > 0 ){ 
      
      $msg .= '<div class="erreur">Ce pseudo ' . $_POST['pseudo'] . ' n\'est pas disponible, veuillez choisir un autre pseudo.</div>';
      
    } else{ 

    $resultat = $pdo -> prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, statut, date_enregistrement) VALUES (:pseudo, :mdp, :nom, :prenom, :email, :civilite, 0, NOW())");

    $resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $mdp_crypte = md5($_POST['mdp']);
    $resultat -> bindParam(':mdp', $mdp_crypte , PDO::PARAM_STR);
    $resultat -> bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
    $resultat -> bindParam(':prenom', $_POST['prenom'], PDO::PARAM_STR);
    $resultat -> bindParam(':email', $_POST['email'], PDO::PARAM_STR);
    $resultat -> bindParam(':civilite', $_POST['civilite'], PDO::PARAM_STR);

      
    $resultat -> execute(); 
    //print_r($pdo->errorInfo());

    
    // if($resultat -> execute()){
      // header('location:index.php');
    // }
    
    
    $msg .= '<div class="validation">L\'inscription est réussie !</div>';
      
    }
  }
}

$pseudo = (isset($_POST['pseudo'])) ? $_POST['pseudo'] : '';
$prenom = (isset($_POST['prenom'])) ? $_POST['prenom'] : '';
$nom = (isset($_POST['nom'])) ? $_POST['nom'] : '';
$email = (isset($_POST['email'])) ? $_POST['email'] : '';
$civilite = (isset($_POST['civilite'])) ? $_POST['civilite'] : '';
$ville = (isset($_POST['ville'])) ? $_POST['ville'] : '';
$adresse = (isset($_POST['adresse'])) ? $_POST['adresse'] : '';
$code_postal = (isset($_POST['code_postal'])) ? $_POST['code_postal'] : '';

// CONNEXION

if(isset($_POST['login'])){
	//debug($_POST); 
	
	$resultat = $pdo -> prepare("SELECT * FROM membre WHERE pseudo = :pseudo");	
	$resultat -> bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
	$resultat -> execute(); 
	
	if($resultat -> rowCount() > 0){ 
		$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
		
		// debug($membre); 
		if(md5($_POST['mdp']) == $membre['mdp']){ 

			//exit();
			
			foreach($membre as $indice => $valeur){
				if($indice != 'mdp'){
					$_SESSION['membre'][$indice] = $valeur;
				}
			}

			// header('location:index.php');
		}
		else{
			$msg .= '<div class="erreur">Erreur de Mot de passe !</div>';
		}
	}
	else{
		$resultat = $pdo -> prepare("SELECT * FROM membre WHERE email = :email");	
		$resultat -> bindParam(':email', $_POST['pseudo'], PDO::PARAM_STR);
		$resultat -> execute(); 
	
		if($resultat -> rowCount() > 0){
			$membre = $resultat -> fetch(PDO::FETCH_ASSOC);
		
			debug($membre);
			debug($_SESSION);
			if(md5($_POST['mdp']) == $membre['mdp']){ // Les deux MDP sont semblables je peux connecter l'utilisateur
			// Je fais une boucle qui va récupérer toutes les infos du membre pour les enregistrer dans la SESSION. J'organise ma SESSION en array Multidimentionnel (membre, panier)
			
				foreach($membre as $indice => $valeur){
					if($indice != 'mdp'){
						$_SESSION['membre'][$indice] = $valeur;
					}
				}
				

				// header('location:profil.php');
				// }
			// else{
				$msg .= '<div class="erreur">Erreur de Mot de passe !</div>';
			}
		}
		$msg .= '<div class="erreur">Erreur de Login !</div>';
	}
}



$page = 'Accueil';

require_once('inc/header.inc.php');
 ?>

<div class=" main container">
<h1>LokiSalle : LA solution pour vos locations de salles de réunion</h1>
<p>Créé en 2017, LokiSalle vous propose un large choix de salles de réunion de différentes dimensions pouvant accueillir de 10 à 100 personnes sur Paris, Bordeaux, Marseille et Lyon.</p>

<p>Nous disposons de petites salles pour travailler avec vos collaborateurs et vos fournisseurs ou pour recevoir vos clients, mais aussi de très grandes salle pour les grandes occasions.</p>

<p>Toutes les salles proposées disposent de toutes les commodités pour la réussite de vos meetings.</p>

<p>Que ce soit pour une réunion d'une heure comme pour un séminaire d'une journée voire plus, les salles de réunion LokiSalle, vous propose gratuitement la présence d'une hôtesse qui accueillera tous les participants pour les aiguiller vers la salle que vous avez réservé. Elle sera à votre service pour préparer des petits déjeuners, sandwichs ou plateaux repas, ou encore réserver un restaurant ou un taxi.</p>

<p>LokiSalle mets tout en œuvre pour vous simplifier la vie et concourir à la réussite de vos réunions.</p>

</div>




<?php 
 	require_once("inc/footer.inc.php");
?>