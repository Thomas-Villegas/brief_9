<?php require_once("inc/init.inc.php"); 

//------ TRAITEMENT PHP ------//
// Si l'utilisateur demande la déconnexion, on détruit la session
if (isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
    session_destroy();
}
// Si l'utilisateur est déjà connecté, on le redirige vers la page de profil
if (userConnect()) {
    header('Location: profil.php');
    exit();
}

// Si le formulaire est envoyé et que tous les champs sont remplis (pseudo et mdp)
if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
    $pseudo = $_POST['pseudo'];
    $mdp = $_POST['mdp'];

    // On vérifie que le pseudo existe dans la base de données
    $requete = $pdo->prepare("SELECT * FROM membre WHERE pseudo = ?");
    $requete->execute(array($pseudo));
    $membre = $requete->fetch(PDO::FETCH_ASSOC);

    // Si le pseudo existe, on vérifie le mot de passe
    if (isset($membre)) {
        // On vérifie que le mot de passe est correct
        if (sha1($mdp) == $membre['mdp']) {
            echo 'Mot de passe correct';
            // On génère un nouvel identifiant de session
            session_regenerate_id();

            // On supprime le mot de passe de la session
            unset($membre['mdp']);
            $_SESSION['membre'] = $membre;

            // On redirige l'utilisateur vers la page de profil
            header('Location: profil.php');
            exit();

        } else { // Si le mot de passe est incorrect
            $contenu .= '<div class="erreur">Mot de passe incorrect</div>';
        }

    } else { // Si le pseudo n'existe pas
        $contenu .= '<div class="erreur">Pseudo incorrect</div>';
    }
}

//------ AFFICHAGE HTML ------//
?>
<?php require_once("inc/haut.inc.php"); ?>

<form method="post" action="" class="form-connexion">
    <label for="pseudo">Pseudo</label>
    <input type="text" id="pseudo" name="pseudo" maxlengh="20" placeholder="Votre pseudo" partern="[a-zA-Z0-9-_.]{1,20}" title="caractère acceptés : a-z A_Z 0-9 -_." required="required">
    
    <label for="mdp">Mot de passe</label>
    <input type="password" id="mdp" name="mdp" required="required">
    
    <input type="submit" value="Se connecter" name="connexion">
</form>
<?php echo $contenu; ?>

<?php require_once("inc/bas.inc.php"); ?>