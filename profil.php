<?php require_once("inc/init.inc.php"); 

//------ TRAITEMENT PHP ------//
// Si l'utilisateur n'est pas connecté, on le redirige vers la page de connexion
if (!userConnect()) {
    header('Location: connexion.php');
    exit();
}

// On récupère les informations de l'utilisateur connecté pour les afficher
$contenu .= '<h1 class="titre-profil">Bonjour ' . $_SESSION['membre']['pseudo'] . '</h1>';
$contenu .= '<div class="contenaire-profil"><h2 class="titre-information"> Voici vos informations </h2>';
$contenu .= '<p> Email : ' . $_SESSION['membre']['email'] . '</p>';
$contenu .= '<p> Adresse : ' . $_SESSION['membre']['adresse'] . '</p>';
$contenu .= '<p> Ville : ' . $_SESSION['membre']['ville'] . '</p>';
$contenu .= '<p> Code postal: ' . $_SESSION['membre']['code_postal'] . '</p></div>';

//------ AFFICHAGE HTML ------//
require_once("inc/haut.inc.php");
echo $contenu;
require_once("inc/bas.inc.php");