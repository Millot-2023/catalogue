// Attendre que le DOM soit entièrement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOMContentLoaded : Le DOM est entièrement chargé.");

    // Fonction utilitaire pour afficher des messages
    function showMessage(targetElement, message, type = 'info') {
        if (!targetElement) {
            console.warn("showMessage: Élément cible non trouvé pour afficher le message.", message);
            return;
        }
        targetElement.textContent = message;
        targetElement.style.padding = '10px';
        targetElement.style.borderRadius = '5px';
        targetElement.style.marginTop = '10px';
        targetElement.style.textAlign = 'center';

        switch (type) {
            case 'success':
                targetElement.style.backgroundColor = '#d4edda';
                targetElement.style.color = '#155724';
                targetElement.style.border = '1px solid #c3e6cb';
                break;
            case 'error':
                targetElement.style.backgroundColor = '#f8d7da';
                targetElement.style.color = '#721c24';
                targetElement.style.border = '1px solid #f5c6cb';
                break;
            case 'warning':
                targetElement.style.backgroundColor = '#fff3cd';
                targetElement.style.color = '#856404';
                targetElement.style.border = '1px solid #ffeeba';
                break;
            case 'info':
            default:
                targetElement.style.backgroundColor = '#d1ecf1';
                targetElement.style.color = '#0c5460';
                targetElement.style.border = '1px solid #bee5eb';
                break;
        }

        setTimeout(() => {
            targetElement.textContent = '';
            targetElement.style = '';
        }, 5000);
    }

    // Début du code pour la navigation
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const mainNav = document.querySelector('.main-nav');

    console.log("DOMContentLoaded : Elements de navigation récupérés.");

    const BREAKPOINT_DESKTOP = 768;

    if (navToggle && navMenu && mainNav) {
        console.log("DOMContentLoaded : Éléments de navigation trouvés. Ajout des écouteurs d'événements.");

        navToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            this.classList.toggle('active');
            navMenu.classList.toggle('active');

            if (navMenu.classList.contains('active')) {
                if (window.innerWidth <= BREAKPOINT_DESKTOP) {
                    navMenu.style.width = '100%';
                }
            } else {
                navMenu.style.width = '';
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > BREAKPOINT_DESKTOP) {
                if (navMenu.classList.contains('active')) {
                    navToggle.classList.remove('active');
                    navMenu.classList.remove('active');
                }
                navMenu.style.width = '';
            }
        });

    } else {
        console.warn("Certains éléments de navigation sont manquants (nav-toggle, nav-menu ou main-nav).");
    }
    // Fin du code pour la navigation


    // Début du code de gestion des articles (ajout, suppression, listage)
    const articleForm = document.getElementById('articleForm');
    const messageArea = document.getElementById('messageArea');

    console.log("Articles : Éléments du formulaire d'article récupérés.");

    if (articleForm) {
        console.log("Articles : Formulaire d'article trouvé. Ajout de l'écouteur de soumission.");
        articleForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log("Articles : Soumission du formulaire détectée.");

            const articleData = {
                titre: document.getElementById('titre').value,
                imageUrl: document.getElementById('imageUrl').value,
                datePublication: document.getElementById('datePublication').value,
                resume: document.getElementById('resume').value,
                contenuComplet: document.getElementById('contenuComplet').value
            };

            // CHEMIN CORRIGÉ POUR add_article.php (ABSOLU)
            let apiUrl = '/catalogue/backend/add_article.php'; 
            const articleIdField = document.getElementById('articleId');

            if (articleIdField && articleIdField.value) {
                // CHEMIN CORRIGÉ POUR update_article.php (ABSOLU)
                apiUrl = '/catalogue/backend/update_article.php';
                articleData.id = articleIdField.value;
                console.log("Edit Article : Mode édition détecté, ID:", articleData.id);
            } else {
                console.log("Add Article : Mode ajout détecté.");
            }

            try {
                console.log("Articles : Tentative d'envoi de l'article à l'API via :", apiUrl);
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(articleData)
                });

                const result = await response.json();
                console.log("Articles : Réponse de l'API d'ajout/mise à jour d'article reçue :", result);

                if (result.success) {
                    showMessage(messageArea, result.message || "Opération réussie !", 'success');
                    if (apiUrl.includes('add_article.php')) {
                        articleForm.reset();
                        console.log("Articles : Article ajouté avec succès. Appel de fetchArticles().");
                        if (document.getElementById('articlesTableBody')) {
                            fetchArticles();
                        }
                    } else {
                        console.log("Articles : Article mis à jour avec succès. Redirection vers l'administration.");
                        window.location.href = 'admin-articles.php';
                    }
                } else {
                    showMessage(messageArea, "Erreur : " + result.message, 'error');
                }

            } catch (error) {
                showMessage(messageArea, "Erreur réseau ou interne lors de l'opération : " + error.message, 'error');
                console.error('Erreur Fetch lors de l\'ajout/mise à jour:', error);
            }
        });
    }

    const articlesTableBody = document.getElementById('articlesTableBody');
    const listMessageArea = document.getElementById('listMessageArea');

    console.log("Articles : Éléments de la liste d'articles récupérés.");

    // CHEMINS CORRIGÉS POUR get_articles.php et delete_article.php (ABSOLUS)
    const fetchArticlesApiUrl = '/catalogue/backend/get_articles.php';
    const deleteArticleApiUrl = '/catalogue/backend/delete_article.php';

    // Fonction pour pré-remplir le formulaire d'édition
    function populateEditForm(article) {
        document.getElementById('articleId').value = '';
        document.getElementById('titre').value = '';
        document.getElementById('imageUrl').value = '';
        document.getElementById('datePublication').value = '';
        document.getElementById('resume').value = '';
        document.getElementById('contenuComplet').value = '';

        if (!article || !article.id_article) {
            console.error("populateEditForm: Article data is invalid or missing ID.");
            const messageAreaEdit = document.getElementById('messageArea');
            if (messageAreaEdit) showMessage(messageAreaEdit, "Article non trouvé ou données invalides.", "error");
            return;
        }

        document.getElementById('articleId').value = article.id_article || '';
        document.getElementById('titre').value = article.titre || '';
        document.getElementById('imageUrl').value = article.image_url || '';
        document.getElementById('datePublication').value = article.date_publication || '';
        document.getElementById('resume').value = article.resume || '';
        document.getElementById('contenuComplet').value = article.contenuComplet || '';

        const messageAreaEdit = document.getElementById('messageArea');
        if (messageAreaEdit) messageAreaEdit.textContent = '';
    }


    // Code pour article-details.html ET edit-article.html
    if (window.location.pathname.includes('article-details.html') || window.location.pathname.includes('edit-article.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const articleId = urlParams.get('id');
        const isEditPage = window.location.pathname.includes('edit-article.html');
        const targetElement = isEditPage ? document.getElementById('articleForm') : document.getElementById('article-detail-content');
        const pageTitleElement = document.getElementById('article-title-display') || document.getElementById('edit-page-title');
        const messageAreaSpecific = document.getElementById('messageArea') || document.getElementById('article-detail-message');

        if (articleId) {
            console.log(`Chargement de l'article ID ${articleId} pour ${isEditPage ? 'édition' : 'affichage des détails'}.`);
            if (messageAreaSpecific) showMessage(messageAreaSpecific, `Chargement de l'article ID ${articleId}...`, 'info');

            // CHEMIN CORRIGÉ POUR get_article_by_id.php (ABSOLU)
            fetch(`/catalogue/backend/get_article_by_id.php?id=${articleId}`)
                .then(response => {
                    console.log("Fetch pour détails/édition: Réponse HTTP reçue, statut:", response.status);
                    if (!response.ok) {
                        return response.json().catch(() => ({ message: `Erreur HTTP: ${response.status}` })).then(err => {
                            throw new Error(err.message || `Erreur HTTP: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(article => {
                    console.log("Données de l'article reçues pour détails/édition :", article);
                    if (article && article.id_article) {
                        if (isEditPage) {
                            populateEditForm(article);
                            if (pageTitleElement) pageTitleElement.textContent = `Modifier l'article : ${article.titre || 'Sans titre'}`;
                            if (messageAreaSpecific) messageAreaSpecific.textContent = '';
                        } else {
                            if (pageTitleElement) pageTitleElement.textContent = article.titre || 'Titre non disponible';
                            if (targetElement) {
                                const contenuCompletAffiche = article.contenuComplet || 'Contenu complet non disponible.';
                                const categorieAffiche = article.categorie || 'Non spécifiée';
                                const datePublicationAffiche = article.date_publication || 'Date non spécifiée';

                                targetElement.innerHTML = `
                                    <figure class="article-image-figure">
                                        <img src="${article.image_url || 'placeholder.jpg'}" alt="Image de l'article : ${titre}">
                                        <figcaption>${article.titre || 'Image de l\'article'}</figcaption>
                                    </figure>
                                    <p><strong>Résumé :</strong> ${article.resume || 'Résumé non disponible'}</p>
                                    <div>
                                        <h3>Contenu Complet :</h3>
                                        <p>${contenuCompletAffiche}</p>
                                    </div>
                                    <p>Catégorie : ${categorieAffiche}</p>
                                    <p>Date de publication : ${datePublicationAffiche}</p>
                                `;
                            }
                            if (messageAreaSpecific) messageAreaSpecific.textContent = '';
                        }
                    } else {
                        const message = "Article non trouvé ou données invalides.";
                        if (pageTitleElement) pageTitleElement.textContent = 'Article non trouvé';
                        if (targetElement) targetElement.innerHTML = `<p>${message}</p>`;
                        if (messageAreaSpecific) showMessage(messageAreaSpecific, message, 'error');
                        if (isEditPage) populateEditForm({});
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des détails de l\'article pour affichage ou édition :', error);
                    const errorMessage = `Impossible de charger les détails de l'article. Erreur: ${error.message}`;
                    if (pageTitleElement) pageTitleElement.textContent = 'Erreur de chargement';
                    if (targetElement) targetElement.innerHTML = `<p>${errorMessage}</p>`;
                    if (messageAreaSpecific) showMessage(messageAreaSpecific, errorMessage, 'error');
                    if (isEditPage) populateEditForm({});
                });
        } else {
            console.log('Aucun ID d\'article trouvé dans l\'URL pour détails/édition.');
            const message = 'Article non spécifié dans l\'URL.';
            if (pageTitleElement) pageTitleElement.textContent = 'Article non spécifié';
            if (targetElement) targetElement.innerHTML = `<p>${message}</p>`;
            if (messageAreaSpecific) showMessage(messageAreaSpecific, message, 'warning');
            if (isEditPage) populateEditForm({});
        }
    }
    async function fetchArticles() {
        console.log("fetchArticles() : Fonction appelée.");
        let targetContainer;
        let targetMessageArea;

        if (document.getElementById('articles-container')) {
            targetContainer = document.getElementById('articles-container');
            targetMessageArea = document.getElementById('message-area');
        } else if (document.getElementById('articlesTableBody')) {
            targetContainer = articlesTableBody;
            targetMessageArea = listMessageArea;
        } else {
            console.warn("fetchArticles(): Aucun conteneur d'articles identifiable trouvé (articles-container ou articlesTableBody).");
            return;
        }

        if (targetMessageArea) {
            showMessage(targetMessageArea, 'Chargement des articles...', 'info');
        }
        if (targetContainer) {
            if (targetContainer.id === 'articles-container') {
                targetContainer.innerHTML = '<p style="text-align: center;">Chargement des articles...</p>';
            } else {
                targetContainer.innerHTML = '<tr><td colspan="6" style="text-align: center;">Chargement des articles...</td></tr>';
            }
        }

        try {
            console.log("fetchArticles() : Tentative de récupération des articles depuis :", fetchArticlesApiUrl);
            const response = await fetch(fetchArticlesApiUrl);
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Erreur HTTP: ${response.status}` }));
                throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
            }
            const data = await response.json();
            console.log("fetchArticles() : Données reçues de l'API :", data);

            if (data.success && data.articles) {
                displayArticles(data.articles, targetContainer, targetMessageArea);
                console.log("fetchArticles() : Articles affichés avec succès.");
            } else {
                const message = data.message || "Aucun article trouvé.";
                if (targetMessageArea) {
                    showMessage(targetMessageArea, "Erreur lors du chargement des articles : " + message, 'error');
                }
                if (targetContainer) {
                    if (targetContainer.id === 'articles-container') {
                        targetContainer.innerHTML = '<p style="text-align: center;">' + message + '</p>';
                    } else {
                        targetContainer.innerHTML = '<tr><td colspan="6" style="text-align: center;">' + message + '</td></tr>';
                    }
                }
                console.warn("fetchArticles() : La réponse de l'API n'indique pas le succès ou ne contient pas d'articles.", data);
            }

        } catch (error) {
            const errorMessage = "Impossible de charger les articles. Erreur: " + error.message;
            if (targetMessageArea) {
                showMessage(targetMessageArea, errorMessage, 'error');
            }
            if (targetContainer) {
                if (targetContainer.id === 'articles-container') {
                    targetContainer.innerHTML = '<p style="text-align: center;">Erreur lors du chargement des articles.</p>';
                } else {
                    targetContainer.innerHTML = '<tr><td colspan="6" style="text-align: center;">Erreur de chargement.</td></tr>';
                }
            }
            console.error("Erreur de récupération des articles:", error);
        }
    }

    function displayArticles(articles, targetContainer, targetMessageArea) {
        console.log("displayArticles() : Fonction appelée avec", articles.length, "articles.");
        if (targetContainer) {
            targetContainer.innerHTML = '';
            if (articles.length === 0) {
                const noArticlesMessage = '<p style="text-align: center;">Aucun article à afficher pour le moment.</p>';
                if (targetContainer.id === 'articles-container') {
                    targetContainer.innerHTML = noArticlesMessage;
                } else {
                    targetContainer.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun article à afficher.</td></tr>';
                }
                if (targetMessageArea) showMessage(targetMessageArea, 'Aucun article à afficher.', 'info');
                return;
            }

            articles.forEach(article => {
                if (targetContainer.id === 'articles-container') {
                    const articleCard = document.createElement('article');
                    articleCard.classList.add('article-card');

                    const imageUrl = article.image_url || 'placeholder.jpg';
                    const titre = article.titre || 'Titre non disponible';
                    const datePublication = article.date_publication || 'Date non spécifiée';
                    const resume = article.resume || 'Résumé non disponible';
                    const articleId = article.id_article || article.id || '';

                    articleCard.innerHTML = `
                        <div class="article-image-container">
                            <img src="${imageUrl}" alt="Image de l'article : ${titre}">
                        </div>
                        <div class="card-content">
                            <h3>${titre}</h3>
                            <p class="article-date">Publié le: ${datePublication}</p>
                            <p>${resume}</p>
                            <div class="card-button-wrapper">
                                <a href="article-details.html?id=${articleId}" class="read-more-btn">Lire la suite</a>
                            </div>
                        </div>
                    `;
                    console.log("GENERATION DU LIEN - ID:", articleId, "TITRE:", titre);
                    targetContainer.appendChild(articleCard);

                } else {
                    const row = targetContainer.insertRow();
                    const articleId = article.id_article || article.id || '';

                    row.innerHTML = `
                        <td>${articleId}</td>
                        <td>${article.titre || ''}</td>
                        <td><img src="${article.image_url || 'placeholder.jpg'}" alt="Image" style="width: 50px; height: auto;"></td>
                        <td>${article.date_publication || ''}</td>
                        <td>${(article.resume || '').substring(0, 70)}...</td>
                        <td class="actions-cell">
                            <a href="edit-article.html?id=${articleId}" class="btn btn-edit">Modifier</a>
                            <button class="btn btn-delete" data-id="${articleId}">Supprimer</button>
                        </td>
                    `;
                }
            });

            if (targetContainer.id === 'articlesTableBody') {
                document.querySelectorAll('.btn-delete').forEach(button => {
                    button.addEventListener('click', async (event) => {
                        const articleId = event.target.dataset.id;
                        if (confirm(`Êtes-vous sûr de vouloir supprimer l'article ID: ${articleId} ? Cette action est irréversible.`)) {
                            await deleteArticle(articleId);
                        }
                    });
                });
            }
        }
    }

    async function deleteArticle(id) {
        console.log("SCRIPT: deleteArticle() - Tentative de suppression de l'article ID:", id);
        const targetMessageArea = document.getElementById('listMessageArea') || document.getElementById('message-area');

        try {
            const params = new URLSearchParams();
            params.append('id', id);

            const response = await fetch(deleteArticleApiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Erreur HTTP: ${response.status}` }));
                throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log("SCRIPT: deleteArticle() - Réponse de l'API de suppression :", data);

            if (data.success) {
                showMessage(targetMessageArea, "Article supprimé avec succès !", 'success');
                fetchArticles();
            } else {
                showMessage(targetMessageArea, "Erreur lors de la suppression : " + data.message, 'error');
            }

        } catch (error) {
            showMessage(targetMessageArea, "Erreur réseau ou interne lors de la suppression: " + error.message, 'error');
            console.error("SCRIPT: Erreur de suppression:", error);
        }
    }

    if (document.getElementById('articles-container') || document.getElementById('articlesTableBody')) {
        console.log("DOMContentLoaded : Appel initial de fetchArticles().");
        fetchArticles();
    }

    console.log("SCRIPT.JS VERSION FINALE : " + new Date().toLocaleString());
});