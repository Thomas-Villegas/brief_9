<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Musicart</title>
        <link rel="stylesheet" href="<?php echo RACINE_SITE; ?>inc/css/style.css">
    </head>
    <body>
        <header>
            <div class="conteneur">
                <div>
                    <a href="#" title="Musicart" id="title">Musicart.com</a>
                </div>
            </div>
            <nav>
                <?php 
                if (userAdmin()) {
                    echo '<a href="' . RACINE_SITE . 'admin/gestion_boutique.php" class="nav-link">Gestion Boutique</a>';
                    echo '<a href="' . RACINE_SITE . 'admin/gestion_commande.php" class="nav-link">Gestion Commande</a>';
                }
                if (userConnect()) {
                    echo '<a href="' . RACINE_SITE . 'index.php" class="nav-link">Accueil</a>';
                    echo '<a href="' . RACINE_SITE . 'boutique.php" class="nav-link">Boutique</a>';
                    echo '<a href="' . RACINE_SITE . 'profil.php" class="nav-link">Profil</a>';
                    echo '<a href="' . RACINE_SITE . 'connexion.php?action=deconnexion" class="nav-link">Déconnexion</a>';
                    echo '<a href="' . RACINE_SITE . 'panier.php" id="icon-caddie">🛒</a>';
                } else {
                    echo '<a href="' . RACINE_SITE . 'index.php" class="nav-link">Accueil</a>';
                    echo '<a href="' . RACINE_SITE . 'boutique.php" class="nav-link">Boutique</a>';
                    echo '<a href="' . RACINE_SITE . 'connexion.php" class="nav-link">Connexion</a>';
                    echo '<a href="' . RACINE_SITE . 'inscription.php" class="nav-link">Inscription</a>';
                    echo '<a href="' . RACINE_SITE . 'panier.php" id="icon-caddie">🛒</a>';
                } ?>
            </nav>
        </header>
        <section>
            <div class="conteneur">