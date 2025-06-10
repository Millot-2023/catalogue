<?php
// Définit l'en-tête pour indiquer que la réponse sera du JSON
header('Content-Type: application/json');
// Permet à n'importe quelle origine d'accéder à cette ressource.
// À FAIRE : Pour la production, remplacez '*' par le domaine spécifique de votre frontend
header('Access-Control-Allow-Origin: *');

// Initialisation de la réponse JSON avec un état d'échec par défaut
$response = ['success' => false, 'message' => ''];

// --- DÉBUT DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---
// Détermination de l'environnement (local ou production)
$config_file = 'db_config.local.php'; // Par défaut, environnement local

// Vérifie si l'hôte du serveur n'est PAS 'localhost' ou '127.0.0.1' (environnements locaux)
if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] !== 'localhost' && $_SERVER['SERVER_NAME'] !== '127.0.0.1') {
    $config_file = 'db_config.prod.php'; // Si ce n'est pas local, c'est la production
}

// Inclut le fichier de configuration approprié
require_once __DIR__ . '/' . $config_file;

// Connexion PDO à la base de données
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Configure PDO pour lancer des exceptions en cas d'erreur
} catch (PDOException $e) {
    // En cas d'erreur de connexion à la base de données, retourne une erreur JSON
    $response['message'] = "Erreur de connexion à la base de données : " . $e->getMessage();
    echo json_encode($response);
    exit(); // Arrête l'exécution du script
}
// --- FIN DE LA GESTION DE LA CONFIGURATION DE LA BASE DE DONNÉES ---


try {
    // Requête SQL pour sélectionner tous les articles depuis la table 'articles'
    // Inclus maintenant 'contenu_complet' et 'created_at' si non déjà inclus
    // Ordonné par 'created_at' (le plus récent en premier)
    $stmt = $pdo->prepare("SELECT id, titre, image_url, date_publication, resume, contenu_complet, created_at FROM articles ORDER BY created_at DESC");
    $stmt->execute();

    // Récupère tous les résultats sous forme de tableau associatif
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Si des articles sont trouvés
    if ($articles) {
        $response['success'] = true;
        $response['message'] = 'Articles chargés avec succès depuis la base de données.';
        $response['articles'] = $articles; // Affecte le tableau des articles récupérés
    } else {
        // Aucun article trouvé dans la base de données
        $response['success'] = true; // C'est un succès, mais il n'y a juste pas d'articles
        $response['message'] = 'Aucun article trouvé dans la base de données.';
        $response['articles'] = []; // Retourne un tableau vide
    }

} catch (PDOException $e) {
    // En cas d'erreur de requête SQL
    $response['message'] = 'Erreur de base de données lors de la récupération des articles : ' . $e->getMessage();
    error_log('Erreur dans get_articles.php : ' . $e->getMessage()); // Log l'erreur pour le débogage
}

// Encode la réponse PHP en format JSON et l'affiche
echo json_encode($response);
?>