<?php require_once("inc/init.inc.php");

//------ TRAITEMENT PHP ------//
// Si le formulaire est envoyé et que tous les champs sont remplis on vérifie que le pseudo et l'email ne sont pas déjà pris
if (isset($_POST['pseudo'])) { 
    $requete = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $requete->bindParam(':pseudo', $_POST['pseudo'], PDO::PARAM_STR);
    $requete->execute();
    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Si le pseudo existe, on affiche un message d'erreur
    if (!empty($resultat)) {
        $contenu .= '<div class="msg-erreur">Pseudo indisponible, veuillez en choisir un autre</div>';
    }

    // Si le pseudo n'existe pas, on vérifie que l'email n'est pas déjà pris
    $requete = $pdo->prepare("SELECT * FROM membre WHERE email = :email");
    $requete->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
    $requete->execute();
    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Si l'email existe, on affiche un message d'erreur
    if (!empty($resultat)) {
        $contenu .= '<div class="msg-erreur">Email indisponible, veuillez en choisir un autre</div>';
    }

    // Si le pseudo et l'email sont disponibles, on insère le nouveau membre dans la base de données
    if (empty($contenu)) {
        $password_hash = sha1($_POST['mdp']);
            $requete = $pdo->prepare("INSERT INTO membre (pseudo, mdp, nom, prenom, email, civilite, ville, code_postal, adresse) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $requete->execute([$_POST['pseudo'], $password_hash, $_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['civilite'], $_POST['ville'], $_POST['code_postal'], $_POST['adresse']]);
            $contenu .= "<div class='validation'>Votre inscription a bien été prise en compte.<a href=\"connexion.php\"><u>Me connecter</u></a></div>";
    }
}

//------ AFFICHAGE HTML ------//
?>
<?php require_once("inc/haut.inc.php"); ?>

<?php echo $contenu; ?>
<form method="post" action="" class="form-inscription">
    <label for="pseudo">Pseudo</label>
    <input type="text" id="pseudo" name="pseudo" maxlengh="20" placeholder="Votre pseudo" partern="[a-zA-Z0-9-_.]{1,20}" title="caractère acceptés : a-z A_Z 0-9 -_." required="required">
    
    <label for="mdp">Mot de passe</label>
    <input type="password" id="mdp" name="mdp" required="required">
    
    <label for="nom">Nom</label>
    <input type="text" id="nom" name="nom" placeholder="Votre nom">
    
    <label for="prenom">Prénom</label>
    <input type="text" id="prenom" name="prenom" placeholder="Votre prénom">
    
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="marco@polo.com">
    
    <label for="civilite">Sexe</label>
    <div class="civilite">
        <input type="radio" name="civilite" value="m" checked="checked"><span class="sexe">Homme</span>
        <input type="radio" name="civilite" value="f"><span class="sexe">Femme</span>
    </div>
    
    <label for="ville">Ville</label>
    <input type="text" id="ville" name="ville" placeholder="Votre ville" pattern="[a-zA-Z0-9-_.]{5,15}" title="caractère acceptés : a-z A-Z 0-9 -_.">
    
    <label for="cp">Code postal</label>
    <input type="text" id="code_postal" name="code_postal" placeholder="Votre code postal" pattern="[0-9]{5}" title="5 chiffres requis : 0-9">
    
    <label for="adresse">Adresse</label>
    <textarea name="adresse" id="adresse" cols="20" rows="3" placeholder="Votre adresse" pattern="[a-zA-Z0-9-_.]{5,15}" title="caractères acceptés :  a-zA-Z0-9-_."></textarea>
    
    <input type="submit" value="S'inscrire" name="inscription">
</form>

<?php require_once("inc/bas.inc.php"); ?>