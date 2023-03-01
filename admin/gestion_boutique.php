<?php require_once('../inc/init.inc.php'); 

//------ TRAITEMENT PHP ------//
// Si l'utilisateur n'est pas admin, on le renvoie vers la page de connexion
if(!userAdmin()){
    header('location:../connexion.php'); // si pas admin, on le renvoie vers la page de connexion
    exit();
}

//----- AJOUT PRODUIT -----//

// Si l'utilisateur clique sur le bouton "ajouter un produit", on affiche le formulaire d'ajout
if(isset($_GET['action']) && ($_GET['action'] == "ajout")) {
    
    $contenu .='<h1 class="titre-bo-boutique">Ajout de Produit</h1>';
    $contenu .='<form action="" enctype="multipart/form-data" method="post" class="form-bo-boutique">';
    $contenu .='<label for="reference">Référence produit</label>';
    $contenu .='<input type="text" name="reference" id="reference" placeholder="N° de référence">';
    $contenu .='<label for="categorie">Catégorie</label>';
    $contenu .='<input type="text" name="categorie" id="categorie" placeholder="Catégorie du produit">';
    $contenu .='<label for="titre">Titre</label>';
    $contenu .='<input type="text" name="titre" id="titre" placeholder="Titre du produit">';
    $contenu .='<label for="description">Description</label>';
    $contenu .='<textarea name="description" id="description" cols="30" rows="10" placeholder="La description du produit"></textarea>';
    $contenu .='<label for="photo">Photo</label>';
    $contenu .='<input type="file" name="photo" id="photo">';
    $contenu .='<label for="prix">Prix</label>';
    $contenu .='<input type="text" name="prix" id="prix" placeholder="Prix unitaire en €">';
    $contenu .='<label for="stock">Stock</label>';
    $contenu .='<input type="text" name="stock" id="stock" placeholder="Nombre de produit">';
    $contenu .='<input type="submit" name="ajout" value="Ajouter le produit">';
    $contenu .='</form>;';
}

// Si le formulaire est envoyé, on enregistre le produit en BDD
if(!empty($_POST['ajout'])) {
    $photo_bdd = ""; 

    // On traite le champ photo s'il n'est pas vide
    if(!empty($_FILES['photo']['name'])) { 
        $nom_photo = $_POST['reference'] . '_' .$_FILES['photo']['name']; // on renomme la photo
        $photo_bdd = RACINE_SITE . "photo/$nom_photo"; // on enregistre le chemin de la photo en BDD
        $photo_dossier = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . "/photo/$nom_photo"; // on enregistre le chemin physique de la photo sur le serveur
        copy($_FILES['photo']['tmp_name'],$photo_dossier); // on copie la photo depuis le dossier temporaire vers le dossier photo
    }

    // On boucle sur le tableau $_POST pour échapper les données
    foreach($_POST as $indice => $valeur) {
        $_POST[$indice] = htmlEntities(addSlashes($valeur));
    }

    // On enregistre le produit en BDD
    $resultat = $pdo->prepare("INSERT INTO produit (reference, categorie, titre, description, photo, prix, stock) VALUES (:reference, :categorie, :titre, :description, :photo, :prix, :stock)");
    $resultat->bindParam(':reference', $_POST['reference'], PDO::PARAM_STR);
    $resultat->bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);
    $resultat->bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
    $resultat->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
    $resultat->bindParam(':photo', $photo_bdd, PDO::PARAM_STR);
    $resultat->bindParam(':prix', $_POST['prix'], PDO::PARAM_STR);
    $resultat->bindParam(':stock', $_POST['stock'], PDO::PARAM_STR);
    $resultat->execute();

    // Si la requête a marché, on affiche un message de confirmation
    if($resultat) {
        $validation .= '<div class="validation">Le produit a bien été ajouté</div>';
    }
}


//----- AFFICHAGE PRODUITS -----//
// Si l'indice "action" est définit dans l'URL et sa valeur est égale à "affichage", on affiche les produits
if(isset($_GET['action']) && $_GET['action'] == "affichage") {
    
    // On récupère les produits en BDD
    $requete = $pdo->prepare("SELECT * FROM produit");
    $requete->execute();
    $resultat = $requete->fetchAll(); 
    
    // On affiche les produits dans un tableau HTML
    $contenu .= '<h2> Affichage des Produits </h2>';
    $contenu .= '<p>Nombre de produit(s) dans la boutique : ' . count($resultat) . '</p>'; // count() est une fonction prédéfinie qui compte le nombre d'éléments dans un tableau ou un objet
    $contenu .= '<table border="1"><tr>';
    
    // On affiche les entêtes du tableau dynamiquement
    $nb_colonnes = $requete->columnCount();
    for ($i = 0; $i < $nb_colonnes; $i++) {
        $colonne = $requete->getColumnMeta($i);
        $contenu .= '<th>' . $colonne['name'] . '</th>';
    }
    
    // On rajoute les entêtes "Modification" et "Suppression"
    $contenu .= '<th>Modification</th>';
    $contenu .= '<th>Suppression</th>';
    $contenu .= '</tr>';
    
    // On affiche les lignes du tableau dynamiquement
    foreach($resultat as $indice => $valeur) {
        $contenu .= '<tr>';
        $contenu .= '<td>' . $valeur[0] . '</td>';
        $contenu .= '<td>' . $valeur[1] . '</td>';
        $contenu .= '<td>' . $valeur[2] . '</td>';
        $contenu .= '<td>' . $valeur[3] . '</td>';
        $contenu .= '<td>' . $valeur[4] . '</td>';
        $contenu .= '<td><img src="' . $valeur[5] . '" width="70" height="70"></td>';
        $contenu .= '<td>' . $valeur[6] . '</td>';
        $contenu .= '<td>' . $valeur[7] . '</td>';
        
        // On rajoute les liens "Modification" et "Suppression" avec l'ID du produit dans l'URL pour les récupérer dans $_GET
        $contenu .= '<td class="bouton-modifier"><a href="?action=modification&id_produit=' . $valeur['id_produit'] . '"><img src="' . RACINE_SITE . 'inc/img/edit.png" alt="edit"></a></td>';
        $contenu .= '<td class="bouton-supprimer"><a href="?action=suppression&id_produit=' . $valeur['id_produit'] . '" OnClick="return(confirm(\'Voulez-vous supprimer l\'article ?\'));"><img src="' . RACINE_SITE . 'inc/img/delete.png" alt="delete"></a></td>';
        $contenu .= '</tr>';
    }
    $contenu .= '</table>';
}

//----- MODIFICATION PRODUIT -----//
// Si l'indice "action" est définit dans l'URL et sa valeur est égale à "modification", on affiche le formulaire de modification
if(isset($_GET['action']) && ($_GET['action'] == "modification")) {
    // On récupère les informations du produit en BDD
    $resultat = $pdo->prepare("SELECT * FROM produit WHERE id_produit = :id_produit");
    $resultat->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $resultat->execute();
    $produit_actuel = $resultat->fetch(PDO::FETCH_ASSOC);
    
    // On affiche le formulaire pré-rempli avec les informations du produit
    $contenu .= '<h1 class="titre-bo-boutique"> Modification d\'un produit </h1>';
    $contenu .= '<form method="post" action="" enctype="multipart/form-data" class="form-bo-boutique">';
    $contenu .= '<label>Référence</label>';
    $contenu .= '<input type="text" name="reference" value="' . $produit_actuel['reference'] . '">';
    $contenu .= '<label>Catégorie</label>';
    $contenu .= '<input type="text" name="categorie" value="' . $produit_actuel['categorie'] . '">';
    $contenu .= '<label>Titre</label>';
    $contenu .= '<input type="text" name="titre" value="' . $produit_actuel['titre'] . '">';
    $contenu .= '<label>Description</label>';
    $contenu .= '<textarea name="description" cols="30" rows="10">' . $produit_actuel['description'] . '</textarea>';
    $contenu .= '<label>Prix</label>';
    $contenu .= '<input type="text" name="prix" value="' . $produit_actuel['prix'] . '">';
    $contenu .= '<label>Stock</label>';
    $contenu .= '<input type="text" name="stock" value="' . $produit_actuel['stock'] . '">';
    $contenu .= '<input type="submit" name="modification" value="Modifier le produit">';
    $contenu .= '</form>';
    
    // Si le formulaire est posté, on modifie le produit en BDD
    if(isset($_POST['modification'])) {
        $resultat = $pdo->prepare("UPDATE produit SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, prix = :prix, stock = :stock WHERE id_produit = :id_produit");
        $resultat->bindParam(':reference', $_POST['reference'], PDO::PARAM_STR);
        $resultat->bindParam(':categorie', $_POST['categorie'], PDO::PARAM_STR);
        $resultat->bindParam(':titre', $_POST['titre'], PDO::PARAM_STR);
        $resultat->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
        $resultat->bindParam(':prix', $_POST['prix'], PDO::PARAM_STR);
        $resultat->bindParam(':stock', $_POST['stock'], PDO::PARAM_STR);
        $resultat->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
        $resultat->execute();
        
        // On redirige vers la page de gestion de la boutique
        header('location:gestion_boutique.php?action=affichage');
    }
}

//----- SUPPRESSION PRODUIT -----//
// Si l'indice "action" est définit dans l'URL et sa valeur est égale à "suppression", on supprime le produit en BDD
if(isset($_GET['action']) && ($_GET['action'] == "suppression")) {
    $resultat = $pdo->prepare("DELETE FROM produit WHERE id_produit = :id_produit");
    $resultat->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $resultat->execute();
    
    // On supprime la photo du produit
    $resultat = $pdo->prepare("SELECT photo FROM produit WHERE id_produit = :id_produit");
    $resultat->bindParam(':id_produit', $_GET['id_produit'], PDO::PARAM_STR);
    $resultat->execute();
    $photo_a_supprimer = $resultat->fetch(PDO::FETCH_ASSOC);
    $chemin_photo_a_supprimer = $_SERVER['DOCUMENT_ROOT'] . RACINE_SITE . $photo_a_supprimer['photo'];
    unlink($chemin_photo_a_supprimer);
    
    // On redirige vers la page gestion_boutique.php
    header('location:gestion_boutique.php?action=affichage');
    exit();
}

//----- LIENS PRODUITS -----//
// Si l'indice "action" n'est pas définit dans l'URL, on affiche les liens de gestion de la boutique
if(!isset($_GET['action'])) {
    $contenu .= '<h1 class="titre-bo-boutique">Gestion Boutique</h1>';
    $contenu .= '<div class="bouton-gestion-boutique"><a href="gestion_boutique.php?action=affichage">Affichage des produits</a></div>';
    $contenu .= '<div class="bouton-gestion-boutique"><a href="gestion_boutique.php?action=ajout">Ajout d\'un produit</a></div>';
}

//----- AFFICHAGE CONTENU -----//
require_once('../inc/haut.inc.php');
echo $validation;
echo $contenu;
require_once('../inc/bas.inc.php'); ?>