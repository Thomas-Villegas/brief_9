<?php require_once("inc/init.inc.php");

//------ TRAITEMENT PHP ------//
// Si l'utilisateur a cliqué sur un produit on affiche sa fiche
if(isset($_GET['id_produit']) && !empty($_GET['id_produit'])) {
    // On récupère les informations du produit
    $requete = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $requete->bindValue(':id_produit', $_GET['id_produit'], PDO::PARAM_INT);
    $requete->execute();
    $resultat = $requete->fetch(PDO::FETCH_ASSOC);
    $resultatPrix = number_format($resultat['prix'], 2, ',', ' ');

    // On affiche les informations du produit dans une fiche HTML
    $contenu .= '<div class="fiche-produit">';
    $contenu .= '<img src="' . $resultat['photo'] . '" alt="' . $resultat['titre'] . '">';
    $contenu .= '<div class="fiche-produit-corps">';
    $contenu .= '<h5 class="fiche-produit-titre">' . $resultat['titre'] . '</h5>';
    $contenu .= '<p class="fiche-produit-prix">' . $resultatPrix . ' €</p>';
    $contenu .= '<p class="fiche-produit-description">' . $resultat['description'] . '</p>';
    $contenu .= '<p class="fiche-produit-stock">Il en reste: <span>' . $resultat['stock'] . '</span></p>';
    $contenu .= '<div class="choix-quantite">';
    $contenu .= '<form method="post">';
    $contenu .= '<select name="quantite" id="quantite">';
    // On affiche une liste déroulante avec les quantités disponibles
    for($i = 1; $i <= $resultat['stock']; $i++) {
        $contenu .= '<option value="' . $i . '">' . $i . '</option>';
    }
    $contenu .= '</select>';
    $contenu .= '<input type="submit" name="ajout_panier" value="Ajouter au panier">';
    $contenu .= '</form>';
    $contenu .= '</div>';
    $contenu .= '</div>';
    $contenu .= '</div>';
    $contenu .= '<div class="lien-retour"><a href="boutique.php">Retour à la boutique ↩</a></div>';

    // Si l'utilisateur clique sur le lien "Ajouter au panier" et que le stock est supérieur à 0 on ajoute le produit au panier avec la fonction addPanier() en lui passant les informations du produit
    if(isset($_POST['ajout_panier']) && $resultat['stock'] > 0) {
        // On ajoute le produit au panier
        addPanier($resultat['titre'], $resultat['id_produit'], $_POST['quantite'], $resultat['prix']);
        // On affiche un message de confirmation
        $contenu .= '<div class="validation">Le produit a bien été ajouté au <a href="panier.php" class="lien-panier">panier</a></div>';
    }

    
} else {
    header("location:boutique.php");
}

//------ AFFICHAGE HTML ------//
require_once("inc/haut.inc.php"); 
echo $contenu;
require_once("inc/bas.inc.php");
?>
