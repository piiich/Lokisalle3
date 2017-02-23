<?php
// CONNEXION BDD
$pdo = new PDO('mysql:host=localhost;dbname=lokisalle', 'root', '', array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
));

// SESSION
session_start();

// CHEMIN
define('RACINE_SITE', '/lokisalle3/'); //Definition d'une constante pour créer le chemin absolue du site

// VARIABLES
$msg="";
$page="";
$contenu="";
// AUTRES INCLUSIONS
require_once('fonctions.inc.php');
?>