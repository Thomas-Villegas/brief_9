<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=brief_mvc_poo', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Échec de la connexion à la base de données : ' . $e->getMessage();
}
session_start();

define("RACINE_SITE", "/brief_9/");

$contenu = "";
$validation = "";

require_once("fonction.inc.php");