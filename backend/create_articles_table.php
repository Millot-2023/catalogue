<?php
// Inclure le fichier de configuration de la base de données
require_once 'db_config.php'; // C'est cette ligne qui rend $pdo disponible

try {
    // Vérifier si la base de données est sélectionnée, sinon la créer
    // Ce n'est pas strictement nécessaire si dbname est déjà dans la chaîne de connexion PDO,
    // mais c'est une étape de vérification parfois utile.

    // Maintenant, nous allons créer la table 'articles' si elle n'existe pas.
    // L'instruction SQL pour créer la table
    $sql = "
    CREATE TABLE IF NOT EXISTS articles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        titre VARCHAR(255) NOT NULL,
        image_url VARCHAR(255),
        date_publication DATE NOT NULL,
        resume TEXT,
        contenu_complet LONGTEXT
    );
    ";

    // Exécuter la requête de création de table
    $pdo->exec($sql); // Erreur: Undefined variable $pdo
    echo "Table 'articles' créée avec succès ou déjà existante.<br>";

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>