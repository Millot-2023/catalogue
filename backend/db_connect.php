<?php
$host = 'localhost';          // L'hôte de la base de données (pour XAMPP, par défaut)
$user = 'root';               // Le nom d'utilisateur par défaut de XAMPP
$password = '';               // IMPORTANT : Le mot de passe par défaut de MySQL sous XAMPP est souvent VIDE (une chaîne de caractères vide)
$db = 'articles_db';          // Le nom de votre base de données
$charset = 'utf8mb4';

// Le DSN (Data Source Name) qui inclut le port spécifique de XAMPP pour MySQL (3306)
$dsn = "mysql:host=$host;dbname=$db;port=3306;charset=$charset"; // <-- Port pour XAMPP

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // echo "Connexion à la base de données réussie avec XAMPP !"; // Vous pouvez commenter cette ligne après le test
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>