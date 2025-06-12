<?php
// Directives pour permettre les requêtes (CORS) et définir le type de contenu JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permet l'accès depuis n'importe quelle origine pour le développement

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
    // En cas d'erreur de connexion, retourne un JSON d'erreur et arrête le script
    echo json_encode(["success" => false, "message" => "Erreur de connexion à la base de données : " . $e->getMessage()]);
    exit();
}

// Récupérer les données POST (en JSON)
//$data = json_decode(file_get_contents("php://input"), true);


// Récupérer les données POST (en JSON) - COMMENTEZ OU SUPPRIMEZ CETTE LIGNE
// $data = json_decode(file_get_contents("php://input"), true);

// Données de test temporaires pour simuler une requête POST JSON
$data = [
    'titre' => 'Mon article de test direct',
    'imageUrl' => 'images/direct_test.jpg',
    'datePublication' => '2025-06-12', // Assurez-vous que la date est au format YYYY-MM-DD
    'resume' => 'Résumé pour le test direct.',
    'contenuComplet' => 'Contenu complet pour le test direct.'
];




// Assigner les données aux variables, avec des valeurs par défaut si non présentes
$titre = $data['titre'] ?? '';
$imageUrl = $data['imageUrl'] ?? '';
$datePublication = $data['datePublication'] ?? '';
$resume = $data['resume'] ?? '';
$contenuComplet = $data['contenuComplet'] ?? '';

// Validation simple des données reçues
if (empty($titre) || empty($imageUrl) || empty($datePublication) || empty($resume) || empty($contenuComplet)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont requis."]);
    exit();
}

// Préparer la requête SQL pour l'insertion des données
// Utilisation de requêtes préparées avec des marqueurs de position (?)
$sql = "INSERT INTO articles (titre, image_url, date_publication, resume, contenu_complet) VALUES (?, ?, ?, ?, ?)";

// Préparation du statement PDO
$stmt = $pdo->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête SQL."]);
    exit();
}

// Exécution de la requête avec les valeurs liées
// PDO::execute() avec un tableau de valeurs est plus simple que bindParam() pour ce cas
try {
    $stmt->execute([$titre, $imageUrl, $datePublication, $resume, $contenuComplet]);
    echo json_encode(["success" => true, "message" => "Article ajouté avec succès!"]);
} catch (PDOException $e) {
    // Gérer les erreurs survenant lors de l'exécution de la requête
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout de l'article : " . $e->getMessage()]);
}

// La connexion PDO et le statement sont automatiquement gérés par PHP à la fin du script.
?>