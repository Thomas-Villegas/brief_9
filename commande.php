<?php
require_once("inc/init.inc.php");

//------ TRAITEMENT PHP ------//

// On récapitule les informations de la commande de l'utilisateur en cours
if(isset($_GET['id_commande']) && !empty($_GET['id_commande'])) {
    $requete = $pdo->prepare("SELECT * FROM commande WHERE id_commande = :id_commande");
    $requete->bindValue(':id_commande', $_GET['id_commande'], PDO::PARAM_INT);
    $requete->execute();
    $resultat = $requete->fetch(PDO::FETCH_ASSOC);

    // On récupère les informations du membre
    $requete = $pdo->prepare("SELECT * FROM membre WHERE id_membre = :id_membre");
    $requete->bindValue(':id_membre', $resultat['id_membre'], PDO::PARAM_INT);
    $requete->execute();
    $membre = $requete->fetch(PDO::FETCH_ASSOC);

    // On récupère les informations du produit
    $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $requete->bindValue(':id_produit', $resultat['id_produit'], PDO::PARAM_INT);
    $requete->execute();
    $produit = $requete->fetch(PDO::FETCH_ASSOC);

    // On affiche un tableau HTML avec toutes les commandes des membres
    $contenu .= '<table class="table">';
    $contenu .= '<tr>';
    $contenu .= '<th>Numéro de commande</th>';
    $contenu .= '<th>Nom du client</th>';
    $contenu .= '<th>Prénom du client</th>';
    $contenu .= '<th>Adresse du client</th>';
    $contenu .= '<th>Code postal du client</th>';
    $contenu .= '<th>Ville du client</th>';
    $contenu .= '<th>Pays du client</th>';
    $contenu .= '<th>Produits</th>';
    $contenu .= '<th>Quantités</th>';
    $contenu .= '<th>Prix total</th>';
    $contenu .= '<th>Date</th>';
    $contenu .= '<th>Statut</th>';
    $contenu .= '</tr>';

    $contenu .= '<tr>';
    $contenu .= '<td>' . $resultat['id_commande'] . '</td>';
    $contenu .= '<td>' . $membre['nom'] . '</td>';
    $contenu .= '<td>' . $membre['prenom'] . '</td>';
    $contenu .= '<td>' . $membre['adresse'] . '</td>';
    $contenu .= '<td>' . $membre['code_postal'] . '</td>';
    $contenu .= '<td>' . $membre['ville'] . '</td>';
    $contenu .= '<td>' . $membre['pays'] . '</td>';
    $contenu .= '<td>' . $produit['titre'] . '</td>';
    $contenu .= '<td>' . $resultat['quantite'] . '</td>';
    $contenu .= '<td>' . $resultat['prix'] . '</td>';
    $contenu .= '<td>' . $resultat['date_enregistrement'] . '</td>';
    $contenu .= '<td>' . $resultat['etat'] . '</td>';
    $contenu .= '</tr>';

    $contenu .= '</table>';
}

//------ AFFICHAGE HTML ------//
require_once("inc/haut.inc.php");
echo $contenu;
require_once("inc/bas.inc.php");

