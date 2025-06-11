<?php
// Informations de connexion à la base de données pour l'environnement LOCAL (XAMPP)
$servername = 'localhost';
$dbname = 'articles_db';
$username = 'root';
$password = '';

/**
 * Fonction pour établir une connexion PDO à la base de données.
 * @return PDO La connexion à la base de données.
 * @throws PDOException Si la connexion échoue.
 */
function getDbConnection() {
    global $servername, $dbname, $username, $password; // Utiliser les variables globales définies ci-dessus

    try {
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mode d'erreur : Lève des exceptions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mode de récupération par défaut : tableaux associatifs
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactiver l'émulation des requêtes préparées (pour de meilleures performances et sécurité)
        ];
        $pdo = new PDO($dsn, $username, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        // Enregistrer l'erreur (pour le débogage, ne pas afficher directement en production)
        error_log("Erreur de connexion à la base de données : " . $e->getMessage());
        // Lever une nouvelle exception pour que les scripts appelants puissent la gérer
        throw new Exception("Impossible de se connecter à la base de données. Veuillez réessayer plus tard.");
    }
}
?>