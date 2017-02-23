<?php 
// var_dump($_SESSION);
 ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>Lokisalle - <?= $page ?></title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link href="css/style.css" rel="stylesheet">
</head>
<body>


  <?php if(userConnecte() && !userAdmin()){ ?>
    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

      <a class="navbar-brand" href="<?= RACINE_SITE ?>index.php">LokiSalle</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="<?= RACINE_SITE ?>index.php">Accueil</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>index.php">Qui sommes nous</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>contact.php">Contact</a></li>
              </ul>
      <ul class="nav navbar-nav navbar-right">
                <li class="active"><a href="<?= RACINE_SITE ?>profil.php">Profil</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>index.php?action=deconnexion">Déconnexion</a></li>          
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

  <?php } elseif(userAdmin()){ ?>

    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

      <a class="navbar-brand" href="<?= RACINE_SITE ?>index.php">LokiSalle</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="<?= RACINE_SITE ?>index.php">Accueil</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>index.php">Qui sommes nous</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>contact.php">Contact</a></li>
              </ul>
      <ul class="nav navbar-nav navbar-right">
                          <li class="active"><a href="<?= RACINE_SITE ?>profil.php">Profil</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Gestion<b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?= RACINE_SITE ?>admin/gestion_des_salles.php">Salles</a></li>
                  <li><a href="<?= RACINE_SITE ?>admin/gestion_des_produits.php">Produits</a></li>
                  <li><a href="<?= RACINE_SITE ?>admin/gestion_membres.php">Membres</a></li>
                  <li><a href="<?= RACINE_SITE ?>admin/gestion_des_avis.php">Avis</a></li>
                  <li><a href="<?= RACINE_SITE ?>admin/gestion_des_commandes.php">Commandes</a></li>
                </ul>
                <li class="active"><a href="#">Statistiques</a></li>
                <li class="active"><a href="<?= RACINE_SITE ?>index.php?action=deconnexion">Déconnexion</a></li>
              </li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

  <?php } else{ ?>

    <nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">

      <a class="navbar-brand" href="<?= RACINE_SITE ?>index.php">LokiSalle</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="<?= RACINE_SITE ?>index.php">Accueil</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>index.php">Qui sommes nous</a></li>
              <li class="active"><a href="<?= RACINE_SITE ?>contact.php">Contact</a></li>
              </ul>
      <ul class="nav navbar-nav navbar-right">
        <li class="active">
                
                  <a href="#" data-toggle="modal" data-target="#inscription-modal">Inscription</a>
                  <div class="modal fade" id="inscription-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                      <div class="loginmodal-container">
                        <h1>Inscription</h1><br>
                        <form method="post">
                          <input type="text" name="pseudo" placeholder="Pseudo*">
                          <input type="text" name="nom" placeholder="Nom">
                          <input type="text" name="prenom" placeholder="Prenom">
                          <input type="text" name="email" placeholder="Email*">
                          <input type="password" name="mdp" placeholder="Mot de passe*">
                          <select name="civilite" id="civilite">
                            <option value="m">Homme</option>
                            <option value="f">Femme</option>
                          </select>
                          <input type="submit" name="inscription" class="login loginmodal-submit" value="Inscription">
                          <?= $msg ?>
                        </form>
                      </div>
                    </div>
                  </div>
                
              </li>
              <li class="active"><a href="#" data-toggle="modal" data-target="#login-modal">Connexion</a>
          <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
              <div class="loginmodal-container">
                <h1>Connexion</h1><br>
                <form method="post">
                  <input type="text" name="pseudo" placeholder="Pseudo">
                  <input type="password" name="mdp" placeholder="Mot de passe">
                  <input type="submit" name="login" class="login loginmodal-submit" value="Connexion">
                </form>
                <div class="login-help">
                  <?= $msg ?>
                  <a href="#">Mot de passe oublié</a>
                </div>
              </div>
            </div>
          </div></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
  <?php } ?>