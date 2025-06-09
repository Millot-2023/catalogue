<?php

// Fichier de configuration de la base de données (db_config.php)

$host = 'localhost';        // L'adresse de votre serveur MySQL (ici, c'est votre propre ordinateur via XAMPP)
$dbname = 'articles_db';    // LE NOM EXACT de la base de données que vous avez créée dans phpMyAdmin
$username = 'root';         // L'utilisateur MySQL par défaut de XAMPP
$password = '';             // Le mot de passe de l'utilisateur 'root' de MySQL avec XAMPP est VIDE par défaut

try {
    // Crée une nouvelle instance de PDO pour se connecter à MySQL
    // 'charset=utf8' est important pour gérer les caractères spéciaux
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);

    // Configure PDO pour qu'il lance des exceptions en cas d'erreur SQL.
    // C'est essentiel pour le débogage et une meilleure gestion des erreurs.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cette ligne est temporaire pour tester la connexion, vous pourrez la supprimer plus tard
    // Ne doit PAS être décommentée en production si le script renvoie du JSON !
    // echo "Connexion à la base de données réussie !";

} catch (PDOException $e) {
    // Si la connexion échoue, affiche un message d'erreur et arrête le script.
    // $e->getMessage() donne des détails sur l'erreur.
    // Note: Pour les API, on renverrait plutôt un JSON d'erreur ici.
    // Mais pour le débogage initial de connexion, un die() est acceptable.
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}