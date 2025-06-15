<?php 
// Active l'affichage de toutes les erreurs PHP pour le débogage. À retirer en production.
error_reporting(E_ALL); 
ini_set('display_errors', 1); 

// Inclusion du header. Le '..' est crucial car admin-articles.php est maintenant dans le dossier 'admin/'.
// Il doit "remonter" d'un niveau pour trouver le dossier 'includes/' à la racine de 'catalogue/'.
include '../includes/header.php'; 
?>

<main class="main-content-wrapper admin-page-content"> 
    <h1>Gestion des Articles</h1>

    <section class="admin-form-section">
        <h2>Ajouter un nouvel article</h2>
        <form id="addArticleForm" class="admin-form">
            <div class="form-group">
                <label for="titre">Titre :</label>
                <input type="text" id="titre" name="titre" required>
            </div>
            <div class="form-group">
                <label for="imageUrl">URL de l'image :</label>
                <input type="url" id="imageUrl" name="imageUrl" required>
            </div>
            <div class="form-group">
                <label for="datePublication">Date de publication :</label>
                <input type="date" id="datePublication" name="datePublication" required>
            </div>
            <div class="form-group">
                <label for="resume">Résumé :</label>
                <textarea id="resume" name="resume" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="contenuComplet">Contenu Complet :</label>
                <textarea id="contenuComplet" name="contenuComplet" rows="6" required></textarea>
            </div>
            <button type="submit" class="button">Ajouter l'article</button>
        </form>
    </section>

    <hr class="section-separator">

<!--LISTE DES ARTICLES EXISTANTS-->
    <section class="admin-list-section">
        <h2>Liste des articles existants</h2>
        <div id="articlesList" class="articles-list">
            <p>Chargement des articles...</p>
        </div>
    </section>
<!--/LISTE DES ARTICLES EXISTANTS-->

<script>
    // --- Chemins vers les scripts PHP (CORRIGÉS pour dossier 'admin/') ---
    // Ces chemins sont corrects car ils remontent d'un dossier (de 'admin/' à 'catalogue/')
    // puis descendent dans 'backend/'.
    const GET_ARTICLES_URL = '../backend/get_articles.php';
    const ADD_ARTICLE_URL = '../backend/add_article.php';
    const DELETE_ARTICLE_URL = '../backend/delete_article.php';
    // --- FIN DES CHEMINS CORRIGÉS ---

    const addArticleForm = document.getElementById('addArticleForm');
    const articlesListDiv = document.getElementById('articlesList');

    // Fonction pour charger et afficher les articles
    async function loadArticles() {
        articlesListDiv.innerHTML = '<p>Chargement des articles...</p>';
        try {
            const response = await fetch(GET_ARTICLES_URL);
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            const data = await response.json(); 

            if (data.success && Array.isArray(data.articles)) {
                articlesListDiv.innerHTML = ''; 
                if (data.articles.length === 0) {
                    articlesListDiv.innerHTML = '<p>Aucun article trouvé. Ajoutez-en un nouveau !</p>';
                    return;
                }
                data.articles.forEach(article => {
                    const articleDiv = document.createElement('div');
                    articleDiv.className = 'article-item admin-article-item';
                    articleDiv.innerHTML = `
                        <img src="${article.image_url}" alt="${article.titre || 'Image d\'article'}" style="max-width:100px; height:auto;">
                        
                        <div class="article-info"> <h3>${article.titre} (ID: ${article.id})</h3>
                            <p>Date: ${article.date_publication}</p>
                            <p>${article.resume}</p>
                        </div>

                        <div class="article-actions"> 
                            <a href="edit-article.html?id=${article.id}" class="button edit-btn">Modifier</a> 
                            <button class="button button-danger delete-btn" data-id="${article.id}">Supprimer</button>
                        </div>
                        `;
                    articlesListDiv.appendChild(articleDiv);
                });
                attachEventListeners();
            } else {
                articlesListDiv.innerHTML = `<p>Erreur lors du chargement des articles: ${data.message || 'Format de données inattendu ou données manquantes.'}</p>`;
                console.error("Erreur de l'API get_articles:", data.message || 'La clé "articles" n\'est pas un tableau ou n\'existe pas dans la réponse.');
            }
        } catch (error) {
            articlesListDiv.innerHTML = `<p>Erreur lors du chargement des articles: ${error.message}. Vérifiez la console pour plus de détails.</p>`;
            console.error('Erreur de chargement des articles:', error);
        }
    }

    // Fonction pour attacher les écouteurs d'événements
    function attachEventListeners() {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', async (event) => {
                const id = event.target.dataset.id;
                console.log("Bouton Supprimer cliqué pour l'ID:", id); 
                if (confirm(`Êtes-vous sûr de vouloir supprimer l'article ID ${id} ?`)) {
                    console.log("Confirmation de suppression acceptée pour l'ID:", id); 
                    try {
                        const response = await fetch(DELETE_ARTICLE_URL, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({ id: id })
                        });
                        const result = await response.json();
                        if (result.success) {
                            alert(result.message);
                            loadArticles();
                        } else {
                            alert(`Échec de la suppression: ${result.message}`);
                        }
                    } catch (error) {
                        console.error('Erreur lors de la suppression:', error);
                        alert('Erreur lors de la suppression de l\'article.');
                    }
                } else { 
                    console.log("Confirmation de suppression ANNULÉE pour l'ID:", id); 
                }
            });
        });
    }

    // Gérer la soumission du formulaire d'ajout
    addArticleForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(addArticleForm);
        const articleData = {};
        formData.forEach((value, key) => {
            articleData[key] = value;
        });

        try {
            const response = await fetch(ADD_ARTICLE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(articleData)
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                addArticleForm.reset();
                loadArticles();
            } else {
                alert(`Échec de l'ajout: ${result.message}`);
                console.error("Erreur de l'API d'ajout:", result.message);
            }
        } catch (error) {
            console.error('Erreur lors de l\'ajout de l\'article:', error);
            alert('Une erreur est survenue lors de l\'ajout de l\'article. Vérifiez la console.');
        }
    });

    // Charger les articles au chargement de la page
    document.addEventListener('DOMContentLoaded', loadArticles);
</script>

<?php include '../includes/footer.php'; ?>