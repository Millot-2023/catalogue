<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permet l'accès depuis n'importe quelle origine pour le développement

// Paramètres de connexion à la base de données
$servername = "localhost";
$username = "root"; // Votre nom d'utilisateur MySQL
$password = ""; // Votre mot de passe MySQL
$dbname = "articles_db"; // Le nom de votre base de données

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Échec de la connexion à la base de données: " . $conn->connect_error]));
}

// Récupérer les données POST
$data = json_decode(file_get_contents("php://input"), true);

$titre = $data['titre'] ?? '';
$imageUrl = $data['imageUrl'] ?? '';
$datePublication = $data['datePublication'] ?? '';
$resume = $data['resume'] ?? '';
$contenuComplet = $data['contenuComplet'] ?? ''; // NOUVEAU CHAMP

// Validation simple des données
if (empty($titre) || empty($imageUrl) || empty($datePublication) || empty($resume) || empty($contenuComplet)) {
    echo json_encode(["success" => false, "message" => "Tous les champs sont requis."]);
    $conn->close();
    exit();
}

// Préparer la requête SQL pour insérer les données
// NOTE : Nous ajoutons 'contenu_complet' ici
$sql = "INSERT INTO articles (titre, image_url, date_publication, resume, contenu_complet) VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Erreur de préparation de la requête: " . $conn->error]);
    $conn->close();
    exit();
}

// Lier les paramètres
// 's' pour string, 's' pour string, 's' pour string, 's' pour string, 's' pour string (pour contenu_complet)
$stmt->bind_param("sssss", $titre, $imageUrl, $datePublication, $resume, $contenuComplet); // AJUSTÉ POUR CONTENU_COMPLET

// Exécuter la requête
if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Article ajouté avec succès!"]);
} else {
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout de l'article: " . $stmt->error]);
}

// Fermer la connexion
$stmt->close();
$conn->close();
?>