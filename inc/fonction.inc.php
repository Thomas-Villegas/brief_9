<?php
// Fonction pour afficher les erreurs
function debug($var, $mode = 1) { // $mode = 1 : affiche le résultat de print_r() / $mode = 2 : affiche le résultat de var_dump()
    echo '<div style="background: orange; padding: 5px; float: right; clear: both; ">'; // On affiche un cadre orange
    $trace = debug_backtrace(); // On récupère la trace de la fonction debug()
    $trace = array_shift($trace); // On enlève la première ligne de la trace qui correspond à la fonction debug()
    echo "Debug demandé dans le fichier : $trace[file] à la ligne $trace[line].<hr>"; // On affiche la ligne et le fichier où se trouve le debug()
    if ($mode === 1) { // Si le mode est égal à 1
        echo '<pre>'; print_r($var); echo '</pre>'; // On affiche le résultat de print_r()
    } else { // Sinon
        echo '<pre>'; var_dump($var); echo '</pre>'; // On affiche le résultat de var_dump()
    }
    echo '</div>'; // On ferme le cadre
}

// Fonction pour vérifier si un utilisateur est connecté
function userConnect() {
    if (!isset($_SESSION['membre'])) { // Si l'utilisateur n'est pas connecté
        return false; // Il n'est pas connecté
    } else { // Sinon
        return true; // Il est connecté
    }
}

// Fonction pour vérifier si un utilisateur est admin
function userAdmin() {
    if (userConnect() && $_SESSION['membre']['statut'] == 1) { // Si l'utilisateur est connecté et que son statut est égal à 1
        return true; // Il est admin
    } else { // Sinon
        return false; // Il n'est pas admin
    }
}

// Fonction pour créer un panier avec $_COOKIE


// Fonction pour ajouter un produit dans le panier avec la quantité choisie en utilisant $_COOKIE
function addPanier($titre, $id_produit, $quantite, $prix) {
    if (!isset($_COOKIE['panier'])) { // Si le panier n'existe pas, on le crée
        $panier = array(); // On crée un tableau
        $panier['titre'] = array(); // On crée un tableau pour les titres
        $panier['id_produit'] = array(); // On crée un tableau pour les id_produit
        $panier['quantite'] = array(); // On crée un tableau pour les quantités
        $panier['prix'] = array(); // On crée un tableau pour les prix
        setcookie('panier', serialize($panier), time() + 3600 * 24 * 7, '/'); // Panier valide 7 jours
    } else { // Sinon
        $panier = unserialize($_COOKIE['panier']); // On récupère le panier dans la superglobale $_COOKIE et on le décode avec unserialize()
    }
    $position_produit = array_search($id_produit, $panier['id_produit']); // On cherche si le produit existe déjà dans le panier
    

    if ($position_produit !== false) { // Si le produit existe déjà dans le panier
        $panier['quantite'][$position_produit] += $quantite; // On ajoute la quantité au produit
    } else { // Sinon
        $panier['titre'][] = $titre; // On ajoute le titre
        $panier['id_produit'][] = $id_produit; // On ajoute l'id_produit
        $panier['quantite'][] = $quantite; // On ajoute la quantité
        $panier['prix'][] = $prix; // On ajoute le prix
    }
    setcookie('panier', serialize($panier), time() + 3600 * 24 * 7, '/'); // Panier valide 7 jours
}

// Fonction pour retirer un produit du panier 
function delProduct($id_produit, $quantite) {
    $panier = unserialize($_COOKIE['panier']); // On récupère le panier dans la superglobale $_COOKIE et on le décode avec unserialize()
    $position_produit = array_search($id_produit, $panier['id_produit']); // On cherche si le produit existe déjà dans le panier
    if ($position_produit !== false) { // Si le produit existe déjà dans le panier
        if ($panier['quantite'][$position_produit] > $quantite) { // Si la quantité du produit est supérieure à la quantité à retirer
            $panier['quantite'][$position_produit] -= $quantite; // On retire la quantité au produit
        } else { // Sinon
            array_splice($panier['titre'], $position_produit, 1); // On retire le titre du produit
            array_splice($panier['id_produit'], $position_produit, 1); // On retire l'id_produit du produit
            array_splice($panier['quantite'], $position_produit, 1); // On retire la quantité du produit
            array_splice($panier['prix'], $position_produit, 1); // On retire le prix du produit
        }
    }
    setcookie('panier', serialize($panier), time() + 3600 * 24 * 7, '/'); // Panier valide 7 jours
}

// Fonction pour calculer le montant total du panier
function totalPanier() {
    if(!isset($_COOKIE['panier'])) return 0; // Si le panier n'existe pas, on retourne 0 (pour éviter une erreur
    $panier = unserialize($_COOKIE['panier']); // On récupère le panier dans la superglobale $_COOKIE et on le décode avec unserialize()
    $total = 0; // On initialise le total à 0
    for ($i = 0; $i < count($panier['id_produit']); $i++) { // On parcourt le tableau des id_produit pour calculer le total
        $total += $panier['quantite'][$i] * $panier['prix'][$i]; // On ajoute le prix du produit multiplié par la quantité
    }
    return $total; // On retourne le total
}


// Fonction pour reinitialiser le stock des produits après une commande validée
function reinitStock($id_commande) {
    $panier = unserialize($_COOKIE['panier']); // On récupère le panier dans la superglobale $_COOKIE et on le décode avec unserialize()
    for ($i = 0; $i < count($panier['id_produit']); $i++) { // On parcourt le tableau des id_produit pour reinitialiser le stock
        $requete = prepare("SELECT stock FROM produit WHERE id_produit = :id_produit"); // On récupère le stock du produit
        $requete->bindParam(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT);
        $requete->execute();

        $stock = $requete->fetch(PDO::FETCH_ASSOC);

        $stock = $stock['stock'] - $panier['quantite'][$i]; // On soustrait la quantité du produit au stock
        $requete = prepare("UPDATE produit SET stock = :stock WHERE id_produit = :id_produit"); // On met à jour le stock du produit
        $requete->bindParam(':stock', $stock, PDO::PARAM_INT);
        $requete->bindParam(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT);
        $requete->execute();

        $requete = prepare("INSERT INTO details_commande (id_commande, id_produit, quantite, prix) VALUES (:id_commande, :id_produit, :quantite, :prix)"); // On insère les détails de la commande
        $requete->bindParam(':id_commande', $id_commande, PDO::PARAM_INT);
        $requete->bindParam(':id_produit', $panier['id_produit'][$i], PDO::PARAM_INT);
        $requete->bindParam(':quantite', $panier['quantite'][$i], PDO::PARAM_INT);
        $requete->bindParam(':prix', $panier['prix'][$i], PDO::PARAM_INT);
        $requete->execute();
    }
}

// Fonction pour vider le panier
function delPanier() {
    setcookie('panier', '', time() - 3600, '/'); // On vide le panier en mettant une date d'expiration dans le passé
}
