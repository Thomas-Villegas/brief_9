<?php 
require_once("inc/init.inc.php");

//------ TRAITEMENT PHP ------//
// On récupère les catégories de la base de données
$requete = $pdo->prepare("SELECT DISTINCT categorie FROM produit");
$requete->execute();
$resultat = $requete->fetchAll(PDO::FETCH_ASSOC);

// On affiche les catégories dans un tableau pour trier les produits
$contenu .= '<table class="table-categorie">';
$contenu .= '<tr>';
$contenu .= '<td><a href="' . RACINE_SITE . 'boutique.php" class="lien-categorie">Tous</a></td>';
foreach ($resultat as $categorie) {
    $contenu .= '<td><a href="?categorie=' . $categorie['categorie'] . '" class="lien-categorie">' . $categorie['categorie'] . '</a></td>';
}
$contenu .= '</tr>';
$contenu .= '</table>';

// On récupère les produits de la base de données en fonction de la catégorie
if(isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $requete = $pdo->prepare("SELECT * FROM produit WHERE categorie = :categorie");
    $requete->bindValue(':categorie', $_GET['categorie'], PDO::PARAM_STR);
    $requete->execute();
    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);

    // On veux afficher les produits dans des cartes
    $contenu .= '<div class="cartes-container">';
    // On veux afficher 3 produits par ligne
    foreach (array_chunk($resultat, 3) as $produits_groupe) {
        $contenu .= '<div class="cartes-ligne">';
        // On affiche les produits avec une boucle
        foreach ($produits_groupe as $produit) {
            $produitPrix = number_format($produit['prix'], 2, ',', ' ');
            
            $contenu .= '<div class="carte">';
            $contenu .= '<img src="' . $produit['photo'] . '" alt="' . $produit['titre'] . '">';
            $contenu .= '<div class="carte-corps">';
            $contenu .= '<h5 class="carte-titre">' . $produit['titre'] . '</h5>';
            $contenu .= '<p class="carte-prix">' . $produitPrix . ' €</p>';
            $contenu .= '<a href="fiche_produit.php?id_produit=' . $produit['id_produit'] . '" class="lien-fiche-produit">Voir le produit</a>';
            $contenu .= '</div>';
            $contenu .= '</div>';
        }
        $contenu .= '</div>';
    }
} else {
    $requete = $pdo->prepare("SELECT * FROM produit");
    $requete->execute();
    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);
    // On veux afficher les produits dans des cartes
    $contenu .= '<div class="cartes-container">';
    // On veux afficher 3 produits par ligne
    foreach (array_chunk($resultat, 3) as $produits_groupe) {
        $contenu .= '<div class="cartes-ligne">';
        // On affiche les produits avec une boucle
        foreach ($produits_groupe as $produit) {
            $contenu .= '<div class="carte">';
            $contenu .= '<img src="' . $produit['photo'] . '" alt="' . $produit['titre'] . '">';
            $contenu .= '<div class="carte-corps">';
            $contenu .= '<h5 class="carte-titre">' . $produit['titre'] . '</h5>';
            $contenu .= '<p class="carte-prix">' . $produit['prix'] . ' €</p>';
            $contenu .= '<a href="fiche_produit.php?id_produit=' . $produit['id_produit'] . '" class="lien-fiche-produit">Voir le produit</a>';
            $contenu .= '</div>';
            $contenu .= '</div>';
        }
        $contenu .= '</div>';
    }
    $contenu .= '</div>';
}

//------ AFFICHAGE HTML ------//
require_once("inc/haut.inc.php");
echo $contenu;
require_once("inc/bas.inc.php");
?>