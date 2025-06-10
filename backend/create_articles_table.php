<?php
// Détermination de l'environnement (local ou production)
// Le fichier de configuration par défaut est celui de l'environnement LOCAL
$config_file = 'db_config.local.php';

// Vérifie si l'hôte du serveur n'est PAS 'localhost' ou '127.0.0.1' (environnements locaux)
// Si l'hôte est différent, cela indique que nous sommes en environnement de production
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    $config_file = 'db_config.prod.php'; // Utiliser le fichier de production
}

// Inclut le fichier de configuration approprié
// __DIR__ garantit que le chemin est correct, peu importe où le script est appelé
require_once __DIR__ . '/' . $config_file;

// Connexion PDO à la base de données
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Lance des exceptions en cas d'erreur
} catch (PDOException $e) {
    // En cas d'erreur de connexion, affiche un message d'erreur et arrête le script
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Maintenant, nous allons créer la table 'articles' si elle n'existe pas.
// L'instruction SQL pour créer la table
$sql = "
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    image_url VARCHAR(255),
    date_publication DATE NOT NULL,
    resume TEXT,
    contenu_complet LONGTEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

// Exécuter la requête de création de table
try {
    $pdo->exec($sql);
    echo "Table 'articles' créée avec succès ou déjà existante.<br>";
} catch (PDOException $e) {
    // En cas d'erreur lors de la création de la table
    die("Erreur lors de la création de la table 'articles' : " . $e->getMessage());
}

// La connexion PDO est automatiquement fermée à la fin du script.
?>