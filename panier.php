<?php
require_once("inc/init.inc.php");

//------ TRAITEMENT PHP ------//

// Si l'utilisateur a un panier en cours avec $_COOKIE['panier']
if (isset($_COOKIE['panier'])) {
    // On récupère le panier dans la session
    $panier = unserialize($_COOKIE['panier']);
    // On récupère le nombre de produits dans le panier
    $nb_produits = count($panier['id_produit']);
} else { // Sinon
    // On initialise le nombre de produits à 0
    $nb_produits = 0;
}

// On affiche un message si le panier est vide
if (empty($nb_produits)) {
    $contenu .= '<div class="erreur">Votre panier est vide</div>';
} else { // Sinon
    // On affiche un tableau HTML avec le contenu du panier
    $contenu .= '<table class="table">';
    $contenu .= '<tr>';
    $contenu .= '<th>Titre</th>';
    $contenu .= '<th>Quantité</th>';
    $contenu .= '<th>Prix unitaire</th>';
    $contenu .= '<th>Prix total</th>';
    $contenu .= '<th>Tout supprimer</th>';
    $contenu .= '<th></th>';
    $contenu .= '</tr>';
    
    // On parcourt le panier pour afficher les produits
    for($i = 0; $i < $nb_produits; $i++) {
        // On récupère les informations du produit
        $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
        $requete->bindValue(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT);
        $requete->execute();
        $produit = $requete->fetch(PDO::FETCH_ASSOC);

        // On affiche les informations du produit
        $contenu .= '<tr>';
        $contenu .= '<td>' . $produit['titre'] . '</td>';
        $contenu .= '<td>' . $panier['quantite'][$i] . '</td>';
        $contenu .= '<td>' . $produit['prix'] . ' €</td>';
        $contenu .= '<td>' . $panier['quantite'][$i] * $produit['prix'] . ' €</td>';
        $contenu .= '<td><a href="?del=' . $i . '" class="lien-panier">Supprimer</a></td>';
        // Si le produit a une quantité supérieure à 1, on affiche le lien "Retirer 1"
        if ($panier['quantite'][$i] > 1) {
            $contenu .= '<td><a href="?moins=' . $i . '" class="lien-panier">Retirer 1</a></td>';
        } else { // Sinon, on affiche une case vide
            $contenu .= '<td></td>';
        }
        $contenu .= '</tr>';
    }
    $contenu .= '</table>';

    // Si l'utilisateur clique sur le lien "Retirer 1"
    if (isset($_GET['moins'])) {
        // On veux savoir combien il veux retirer de produit
        $quantite = $panier['quantite'][$_GET['moins']];

        // On retire 1 produit du panier
        delProduct($panier['id_produit'][$_GET['moins']], 1);
        // On redirige l'utilisateur vers la page panier.php
        header('location:panier.php');
    }
    
    // Si l'utilisateur clique sur le lien "Supprimer"
    if (isset($_GET['del'])) {
        // On supprime le produit du panier
        delProduct($panier['id_produit'][$_GET['del']], $panier['quantite'][$_GET['del']]);
        // On redirige l'utilisateur vers la page panier.php
        header('location:panier.php');
    }


    // On calcule le montant total du panier
    $contenu .= '<div class="montant-total">Montant total : ' . totalPanier() . ' €</div>';
    
    // On affiche le bouton "Vider le panier" avec une confirmation
    $contenu .= '<div class="vider-panier"><a href="?vider" onclick="return(confirm(\'Etes-vous sûr de vouloir vider votre panier ?\'));">Vider le panier</a></div>';

    // Si l'utilisateur clique sur le lien "Vider le panier"
    if (isset($_GET['vider'])) {
        // On vide le panier
        delPanier();
        // On redirige l'utilisateur vers la page panier.php
        header('location:panier.php');
    }
    // On affiche le bouton "Payer"
    $contenu .= '<form method="post" action="panier.php">';
    $contenu .= '<input type="submit" name="payer" value="Payer" class="lien-payer">';
    $contenu .= '</form>';

    
    // Si l'utilisateur clique sur "payer" on enregistre la commande en BDD, redirige l'utilisateur vers la page commande.php et vide le panier
    if (isset($_POST['payer'])) {
        // On enregistre la commande en BDD
        $requete = $pdo->prepare("INSERT INTO commande (id_membre, montant, date_enregistrement, etat) VALUES (:id_membre, :montant, NOW(), 'en cours')");
        $requete->bindValue(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_INT); // On récupère l'id du membre connecté
        $requete->bindValue(':montant', totalPanier(), PDO::PARAM_STR); // On récupère le montant total du panier
        $requete->execute();
        // On récupère l'id de la commande
        $id_commande = $pdo->lastInsertId();
        // On parcourt le panier pour enregistrer les produits
        for($i = 0; $i < $nb_produits; $i++) {
            // On récupère les informations du produit
            $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
            $requete->bindValue(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT); // On récupère l'id du produit
            $requete->execute();
            $produit = $requete->fetch(PDO::FETCH_ASSOC);

            // On enregistre le produit dans la table details_commande
            $requete = $pdo->prepare("INSERT INTO details_commande (id_commande, id_produit, quantite, prix) VALUES (:id_commande, :id_produit, :quantite, :prix)");
            $requete->bindValue(':id_commande', $id_commande, PDO::PARAM_INT);
            $requete->bindValue(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT);
            $requete->bindValue(':quantite', $panier['quantite'][$i], PDO::PARAM_INT);
            $requete->bindValue(':prix', $produit['prix'], PDO::PARAM_STR);
            $requete->execute();
        }
        // On vide le panier
        delPanier();

        // On redirige l'utilisateur vers la page commande.php
        header('location:commande.php?id_commande=' . $id_commande . '');
    }
    
    
}
//------ AFFICHAGE HTML ------//
require_once("inc/haut.inc.php");
echo $contenu;
require_once("inc/bas.inc.php");