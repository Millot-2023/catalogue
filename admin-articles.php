<?php include 'includes/header.php'; ?>

    <h1>Gestion des Articles</h1>

    <h2>Ajouter un nouvel article</h2>
    <form id="addArticleForm">
        <div>
            <label for="titre">Titre :</label>
            <input type="text" id="titre" name="titre" required>
        </div>
        <div>
            <label for="imageUrl">URL de l'image :</label>
            <input type="url" id="imageUrl" name="imageUrl" required>
        </div>
        <div>
            <label for="datePublication">Date de publication :</label>
            <input type="date" id="datePublication" name="datePublication" required>
        </div>
        <div>
            <label for="resume">Résumé :</label>
            <textarea id="resume" name="resume" rows="3" required></textarea>
        </div>
        <div>
            <label for="contenuComplet">Contenu Complet :</label>
            <textarea id="contenuComplet" name="contenuComplet" rows="6" required></textarea>
        </div>
        <button type="submit">Ajouter l'article</button>
    </form>

<hr class="section-separator"> 

    <h2>Liste des articles existants</h2>
    <div id="articlesList">
        <p>Chargement des articles...</p>
    </div>

    <div id="editModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:8px;">
            <h3>Modifier l'article</h3>
            <form id="editArticleForm">
                <input type="hidden" id="editId" name="id">
                <div>
                    <label for="editTitre">Titre :</label>
                    <input type="text" id="editTitre" name="titre" required>
                </div>
                <div>
                    <label for="editImageUrl">URL de l'image :</label>
                    <input type="url" id="editImageUrl" name="imageUrl" required>
                </div>
                <div>
                    <label for="editDatePublication">Date de publication :</label>
                    <input type="date" id="editDatePublication" name="datePublication" required>
                </div>
                <div>
                    <label for="editResume">Résumé :</label>
                    <textarea id="editResume" name="resume" rows="3" required></textarea>
                </div>
                <div>
                    <label for="editContenuComplet">Contenu Complet :</label>
                    <textarea id="editContenuComplet" name="contenuComplet" rows="6" required></textarea>
                </div>
                <button type="submit">Enregistrer les modifications</button>
                <button type="button" id="closeEditModal">Annuler</button>
            </form>
        </div>
    </div>

    <script>
        // --- Chemins vers les scripts PHP (CORRIGÉS) ---
        const GET_ARTICLES_URL = 'backend/get_articles.php';
        const ADD_ARTICLE_URL = 'backend/add_article.php';
        const UPDATE_ARTICLE_URL = 'backend/update_article.php';
        const DELETE_ARTICLE_URL = 'backend/delete_article.php';
        // --- FIN DES CHEMINS CORRIGÉS ---

        const addArticleForm = document.getElementById('addArticleForm');
        const articlesListDiv = document.getElementById('articlesList');

        const editModal = document.getElementById('editModal');
        const editArticleForm = document.getElementById('editArticleForm');
        const closeEditModalBtn = document.getElementById('closeEditModal');

        // Fonction pour charger et afficher les articles
        async function loadArticles() {
            articlesListDiv.innerHTML = '<p>Chargement des articles...</p>';
            try {
                const response = await fetch(GET_ARTICLES_URL);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                const data = await response.json(); // Renommé 'data' pour éviter la confusion avec 'articles' de la réponse PHP

                // CORRECTION MAJEURE ICI : utiliser data.articles partout
                if (data.success && Array.isArray(data.articles)) {
                    articlesListDiv.innerHTML = ''; // Efface le message de chargement
                    if (data.articles.length === 0) {
                        articlesListDiv.innerHTML = '<p>Aucun article trouvé. Ajoutez-en un nouveau !</p>';
                        return;
                    }
                    data.articles.forEach(article => {
                        const articleDiv = document.createElement('div');
                        articleDiv.className = 'article-item'; // Simple classe pour identifier
                        // DÉBUT DE LA MODIFICATION CRUCIALE DE LA STRUCTURE HTML DE L'ARTICLE
                        articleDiv.innerHTML = `
                            <img src="${article.image_url}" alt="${article.titre || 'Image d\'article'}" style="max-width:100px; height:auto;">
                            
                            <div class="article-info"> <h3>${article.titre} (ID: ${article.id})</h3>
                                <p>Date: ${article.date_publication}</p>
                                <p>${article.resume}</p>
                            </div>

                            <div class="article-actions"> <button class="edit-btn" 
                                        data-id="${article.id}" 
                                        data-titre="${article.titre}" 
                                        data-imageurl="${article.image_url}" 
                                        data-datepublication="${article.date_publication}" 
                                        data-resume="${article.resume}" 
                                        data-contenucomplet="${article.contenu_complet}">Modifier</button>
                                <button class="delete-btn" data-id="${article.id}">Supprimer</button>
                            </div>
                            `;
                        // FIN DE LA MODIFICATION CRUCIALE DE LA STRUCTURE HTML DE L'ARTICLE
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

            document.querySelectorAll('.edit-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const btn = event.target;
                    // Pré-remplir la modale avec les données de l'article
                    document.getElementById('editId').value = btn.dataset.id;
                    document.getElementById('editTitre').value = btn.dataset.titre;
                    document.getElementById('editImageUrl').value = btn.dataset.imageurl;
                    document.getElementById('editDatePublication').value = btn.dataset.datepublication;
                    document.getElementById('editResume').value = btn.dataset.resume;
                    document.getElementById('editContenuComplet').value = btn.dataset.contenucomplet;
                    editModal.style.display = 'flex'; // Afficher la modale
                });
            });
        }

        // Gérer la fermeture de la modale de modification
        closeEditModalBtn.addEventListener('click', () => {
            editModal.style.display = 'none';
            editArticleForm.reset();
        });

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

        // Gérer la soumission du formulaire de modification
        editArticleForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const formData = new FormData(editArticleForm);
            const articleData = {};
            formData.forEach((value, key) => {
                articleData[key] = value;
            });

            try {
                const response = await fetch(UPDATE_ARTICLE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(articleData)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    editModal.style.display = 'none'; // Cacher la modale après succès
                    loadArticles(); // Recharger les articles pour voir la modification
                } else {
                    alert(`Échec de la modification: ${result.message}`);
                    console.error("Erreur de l'API de modification:", result.message);
                }
            } catch (error) {
                console.error('Erreur lors de la modification de l\'article:', error);
                alert('Une erreur est survenue lors de la modification de l\'article. Vérifiez la console.');
            }
        });

        // Charger les articles au chargement de la page
        document.addEventListener('DOMContentLoaded', loadArticles);
    </script>
<?php include 'includes/footer.php'; ?>