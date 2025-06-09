# PROMPT DE CRÉATION - Catalogue d'Articles Dynamique

---

## Contexte du Projet

Ce prompt récapitule l'intégralité du projet de "Catalogue d'Articles Dynamique" développé. Il est destiné à servir de base de connaissances pour la reproduction ou l'extension de projets similaires, en particulier pour des besoins de gestion de contenu simple via fichiers plats. L'objectif principal était de créer un système flexible où les articles sont stockés sous forme de fichiers HTML simples et sont dynamiquement affichés sur une page principale, avec un backend PHP pour la gestion des données.

## Stack Technologique Utilisée

* **Serveur Local :** XAMPP (Apache, PHP)
* **Backend :** PHP (pour l'API de données)
* **Frontend :** HTML5, CSS3, JavaScript (Vanilla JS)
* **Base de données :** Non utilisée pour le contenu des articles (gestion par fichiers HTML directs).

## Structure du Projet (Dossier `catalogue/` à la racine du serveur web `htdocs/`)

catalogue/
├── articles_html/                   # Contient tous les fichiers HTML de chaque article (ex: historique-developpement.html, feuille-de-route-ce-qu-il-reste-a-accomplir.html)
│   ├── historique-developpement.html
│   ├── feuille-de-route-ce-qu-il-reste-a-accomplir.html # Anciennement "exemple-article2.html", renommé pour clarté.
├── backend/
│   ├── get_articles.php             # Script PHP principal pour lire les articles et générer le JSON
│   ├── [autres_scripts_admin].php   # (Optionnel) Scripts PHP pour futures fonctionnalités d'admin (ajout, suppression, édition - non implémenté en production)
├── css/                             # Feuilles de style CSS
├── images/                          # Images du site (incluant les images des articles)
├── js/                              # Fichiers JavaScript
├── admin-articles.html              # Page d'administration (squelette HTML existant, fonctionnalités à développer dans un clone)
├── articles.html                    # Page principale affichant toutes les cartes d'articles dynamiquement
├── index.html                       # Page d'accueil du site
└── [autres_fichiers_racine].html    # (Ex: .htaccess, robots.txt, etc.)


## Fonctionnalités Implémentées et Logique Clé

### 1. Stockage des Articles

* Chaque article est un fichier HTML autonome (`.html`) stocké dans le dossier `catalogue/articles_html/`.
* Ces fichiers HTML contiennent l'intégralité du contenu de l'article (titre, introduction, sections, images, etc.).
* Des **IDs HTML spécifiques** sont utilisés pour permettre l'extraction automatique des données par le script PHP :
    * `<h1 id="article-main-title">...</h1>` : Pour le titre de l'article (utilisé sur la carte et la page complète).
    * `<img id="card-main-image" src="..." ...>` : Pour l'image principale de l'article (utilisée sur la carte et la page complète).
    * `<div id="article-introduction">...</div>` : Pour le résumé/introduction de l'article (utilisé sur la carte).

### 2. Backend PHP (`backend/get_articles.php`)

* **Rôle :** Scanner et lire tous les fichiers `.html` présents dans le dossier `articles_html/`, extraire les informations pertinentes via `DOMDocument`, et les retourner au frontend sous forme de JSON.
* **Gestion des chemins d'images pour le Frontend :**
    * **Problème initial :** Les chemins d'images dans les articles HTML étaient relatifs à leur emplacement (`../images/image.png`). Si ce chemin était passé tel quel au frontend, il devenait incorrect pour la page `articles.html` (qui est à la racine du projet).
    * **Solution critique :** Le script PHP nettoie le chemin de l'image extrait en supprimant `../` (`../images/image.png` devient `images/image.png`) avant de l'inclure dans le JSON. Cela assure que l'URL de l'image est toujours relative à la racine du dossier `catalogue/` (où se trouve `articles.html`).
    * *Extrait de code clé :*
        ```php
        $imageUrl = str_replace('../', '', $rawImageUrl);
        ```
* **Output JSON :** Chaque article est représenté par un objet JSON contenant a minima : `id` (incrémenté), `titre`, `image_url` (le chemin nettoyé), `date_publication` (actuellement statique), `resume` (tronqué à partir de l'introduction), et `article_full_path` (le chemin du fichier HTML relatif à la racine du projet, ex: `articles_html/nom_fichier.html`).

### 3. Frontend JavaScript (dans `articles.html`)

* **Rôle :** Effectuer une requête `fetch` asynchrone à `backend/get_articles.php` pour récupérer le JSON des articles, puis générer dynamiquement les cartes d'articles sur la page `articles.html`.
* **Génération des cartes :** Pour chaque article dans le JSON, un nouvel élément `<article>` est créé dynamiquement avec les données (`titre`, `image_url`, `resume`, `date_publication`).
* **Liens "Lire la suite" :** Le bouton "Lire la suite" utilise `article.article_full_path` fourni par le PHP pour créer un lien direct vers le fichier HTML complet de l'article.

### 4. Gestion des Chemins Relatifs et Noms de Fichiers

* **Problème des ressources internes des articles :** Lorsque les fichiers HTML d'articles ont été déplacés dans `articles_html/`, leurs propres ressources (CSS, JS, images internes, liens de navigation dans le header/footer) cessaient de se charger car leurs chemins étaient initialement relatifs à la racine (`css/styles.css`).
* **Solution des chemins internes critiques :** Tous les chemins vers les ressources externes (CSS, JS, images) et les liens de navigation *à l'intérieur de chaque fichier HTML d'article* (`articles_html/*.html`) ont été ajustés pour remonter d'un niveau.
    * *Exemples de corrections de chemins :*
        ```html
        <link rel="stylesheet" href="../css/styles.css">
        <img id="card-main-image" src="../images/hero-1920x1000.png">
        <script src="../js/script.js" defer></script>
        <a href="../index.html"> (pour la navigation vers les pages principales)
        ```
* **Optimisation des noms de fichiers d'articles :** Les noms de fichiers d'articles sont désormais choisis pour être pertinents, clairs et SEO-friendly (ex: `feuille-de-route-ce-qu-il-reste-a-accomplir.html` au lieu de `exemple-article2.html`). Le système gère dynamiquement ces nouveaux noms.

## Points de Vigilance et Leçons Apprises (À retenir pour tout projet similaire)

* **Cache Navigateur :** Toujours insister sur la nécessité de vider le cache du navigateur (`Ctrl + F5` / `Cmd + Shift + R`) après toute modification du code backend (PHP) ou des ajustements de chemins frontend, pour s'assurer que le navigateur charge les dernières versions des fichiers et des données.
* **Chemins Relatifs :** C'est une source fréquente de "Not Found" et de bugs d'affichage. Il est crucial de bien distinguer le contexte du chemin :
    * Le chemin tel qu'il est écrit dans le HTML et interprété par le *navigateur*.
    * Le chemin tel qu'il est lu et traité par le *script PHP* (sur le serveur).
    * Le chemin qui est *retourné par l'API JSON* et utilisé par le JavaScript du frontend.
* **Débogage PHP :** Les "Warnings" PHP, même s'ils ne bloquent pas l'exécution, doivent être traités car ils peuvent indiquer des problèmes sous-jacents ou devenir des erreurs fatales dans d'autres contextes.

## Articles Actuels (Exemples de contenu pour le système)

* `historique-developpement.html` : Contient le récapitulatif détaillé de toutes les étapes de développement du projet jusqu'à présent.
* `feuille-de-route-ce-qu-il-reste-a-accomplir.html` : Contient la feuille de route et les fonctionnalités à venir pour le projet.

## Prochaines Étapes Envisagées

* **Dynamiser la date de publication :** Intégrer une balise spécifique dans les fichiers HTML d'articles pour que PHP puisse extraire et afficher la date de publication.
* **Implémenter des catégories/tags :** Ajouter la possibilité d'assigner des catégories ou des mots-clés aux articles pour une meilleure organisation et filtrage.
* **Mettre en place la pagination :** Développer un système de pagination sur `articles.html` pour gérer de grands volumes de contenu.
* **Développer une interface d'administration :** Créer une interface (`admin-articles.html`) pour faciliter l'ajout/édition/suppression des articles sans manipulation directe des fichiers HTML.

## Vision à Long Terme : Autonomie du Projet

Une vision plus ambitieuse explore la possibilité de rendre le projet **entièrement autonome et portable**. L'idée est d'intégrer un environnement serveur minimal (similaire à un XAMPP portable) directement dans le dossier `catalogue/`. Cela permettrait au site de fonctionner depuis une clé USB ou un dossier copié, sans nécessiter l'installation préalable d'un serveur web externe sur la machine hôte. Ceci représenterait un saut technologique significatif vers une portabilité et une facilité de déploiement maximales.

---

**3. Enregistrez le fichier.**

Votre prompt est maintenant parfaitement à jour ! Il est prêt à servir de référence ultra-complète pour tous vos futurs projets.