<?php
header('Content-Type: text/plain'); // Set header to plain text for direct viewing

// Include the database configuration file
require_once __DIR__ . '/db_config.local.php'; // Ensure this path is correct

// Attempt to connect to the database
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion à la base de données réussie !";
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
}
?>