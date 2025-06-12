// Attendre que le DOM soit entièrement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOMContentLoaded : Le DOM est entièrement chargé."); // TRACE 1

    // --- Fonction utilitaire pour afficher des messages ---
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

        // Effacer le message après quelques secondes
        setTimeout(() => {
            targetElement.textContent = '';
            targetElement.style = ''; // Réinitialise tous les styles
        }, 5000); // Message disparaît après 5 secondes
    }

    // --- Début du code existant pour la navigation ---
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const mainNav = document.querySelector('.main-nav');

    console.log("DOMContentLoaded : Elements de navigation récupérés."); // TRACE 2

    const BREAKPOINT_DESKTOP = 768; // À AJUSTER si votre $breakpoint-mobile est différent

    if (navToggle && navMenu && mainNav) {
        console.log("DOMContentLoaded : Éléments de navigation trouvés. Ajout des écouteurs d'événements."); // TRACE 3

        // Logique de basculement du menu au clic sur le bouton burger
        navToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            this.classList.toggle('active');
            navMenu.classList.toggle('active');

            if (navMenu.classList.contains('active')) {
                // S'assure que le menu prend 100% de la largeur du viewport en mode mobile
                if (window.innerWidth <= BREAKPOINT_DESKTOP) {
                    navMenu.style.width = '100%';
                }
            } else {
                // Réinitialise la largeur si le menu est fermé (pour éviter des problèmes de responsive sur desktop)
                navMenu.style.width = ''; // Ou 'auto' si nécessaire
            }
        });

        // Ajout d'un écouteur sur le redimensionnement pour gérer les transitions
        // Si l'écran passe du mobile au desktop, le menu doit se fermer
        window.addEventListener('resize', function() {
            if (window.innerWidth > BREAKPOINT_DESKTOP) {
                if (navMenu.classList.contains('active')) {
                    navToggle.classList.remove('active');
                    navMenu.classList.remove('active');
                }
                navMenu.style.width = ''; // Réinitialise la largeur si on passe en desktop
            }
        });

    } else {
        console.warn("Certains éléments de navigation sont manquants (nav-toggle, nav-menu ou main-nav)."); // TRACE WARN NAV
    }
    // --- Fin du code existant pour la navigation ---


    // --- Début du code de gestion des articles (ajout, suppression, listage) ---
    const articleForm = document.getElementById('articleForm');
    const messageArea = document.getElementById('messageArea'); // Pour admin-articles.html ou add-article.html

    console.log("Articles : Éléments du formulaire d'article récupérés."); // TRACE 6

    if (articleForm) {
        console.log("Articles : Formulaire d'article trouvé. Ajout de l'écouteur de soumission."); // TRACE 7
        articleForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            console.log("Articles : Soumission du formulaire détectée."); // TRACE 8

            const articleData = {
                titre: document.getElementById('titre').value,
                imageUrl: document.getElementById('imageUrl').value,
                datePublication: document.getElementById('datePublication').value,
                resume: document.getElementById('resume').value,
                contenuComplet: document.getElementById('contenuComplet').value
            };

            // Déterminer l'API URL basée sur la page (add_article.php ou update_article.php)
            let apiUrl = 'backend/add_article.php';
            const articleIdField = document.getElementById('articleId'); // Champ caché pour l'ID en mode édition

            if (articleIdField && articleIdField.value) { // Si l'ID existe, c'est une mise à jour
                apiUrl = 'backend/update_article.php';
                articleData.id = articleIdField.value; // Ajoute l'ID aux données pour la mise à jour
                console.log("Edit Article : Mode édition détecté, ID:", articleData.id);
            } else {
                console.log("Add Article : Mode ajout détecté.");
            }

            try {
                console.log("Articles : Tentative d'envoi de l'article à l'API via :", apiUrl); // TRACE 9
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(articleData)
                });

                const result = await response.json();
                console.log("Articles : Réponse de l'API d'ajout/mise à jour d'article reçue :", result); // TRACE 10

                if (result.success) {
                    showMessage(messageArea, result.message || "Opération réussie !", 'success');
                    if (apiUrl.includes('add_article.php')) {
                        articleForm.reset(); // Vider le formulaire seulement pour l'ajout
                        console.log("Articles : Article ajouté avec succès. Appel de fetchArticles()."); // TRACE 11
                        // Si nous sommes sur admin-articles, rafraîchir la liste
                        if (document.getElementById('articlesTableBody')) {
                            fetchArticles();
                        }
                    } else { // C'est une mise à jour
                        console.log("Articles : Article mis à jour avec succès. Redirection vers l'administration.");
                        // REDIRECTION AJOUTÉE ICI POUR LA MISE À JOUR
                        window.location.href = 'admin-articles.html';
                    }
                } else {
                    showMessage(messageArea, "Erreur : " + result.message, 'error');
                }

            } catch (error) {
                showMessage(messageArea, "Erreur réseau ou interne lors de l'opération : " + error.message, 'error');
                console.error('Erreur Fetch lors de l\'ajout/mise à jour:', error); // TRACE ERROR ADD/UPDATE
            }
        });
    }

    const articlesTableBody = document.getElementById('articlesTableBody'); // Pour admin-articles.html
    const listMessageArea = document.getElementById('listMessageArea'); // Pour admin-articles.html

    console.log("Articles : Éléments de la liste d'articles récupérés."); // TRACE 12

    const fetchArticlesApiUrl = 'backend/get_articles.php';
    const deleteArticleApiUrl = 'backend/delete_article.php';

    // Fonction pour pré-remplir le formulaire d'édition (utilisée sur edit-article.html)
    function populateEditForm(article) {
        // Effacer les champs avant de les remplir pour s'assurer de la propreté
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

        // Assurez-vous que ces ID correspondent aux ID de vos champs dans edit-article.html
        document.getElementById('articleId').value = article.id_article || ''; // Champ caché pour l'ID
        document.getElementById('titre').value = article.titre || '';
        document.getElementById('imageUrl').value = article.image_url || '';
        document.getElementById('datePublication').value = article.date_publication || '';
        document.getElementById('resume').value = article.resume || '';
        document.getElementById('contenuComplet').value = article.contenuComplet || '';

        const messageAreaEdit = document.getElementById('messageArea');
        if (messageAreaEdit) messageAreaEdit.textContent = ''; // Efface tout message de chargement précédent
    }


    // MODIFICATION DE LA SECTION DE DÉTAILS/ÉDITION POUR GÉRER LES DEUX PAGES
    // --- Code pour article-details.html ET edit-article.html ---
    // Vérifie si nous sommes sur la page article-details.html OU edit-article.html
    if (window.location.pathname.includes('article-details.html') || window.location.pathname.includes('edit-article.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const articleId = urlParams.get('id');
        const isEditPage = window.location.pathname.includes('edit-article.html');
        const targetElement = isEditPage ? document.getElementById('articleForm') : document.getElementById('article-detail-content'); // Utilisez articleForm
        const pageTitleElement = document.getElementById('article-title-display') || document.getElementById('edit-page-title');
        const messageAreaSpecific = document.getElementById('messageArea') || document.getElementById('article-detail-message');

        if (articleId) {
            console.log(`Chargement de l'article ID ${articleId} pour ${isEditPage ? 'édition' : 'affichage des détails'}.`);
            if (messageAreaSpecific) showMessage(messageAreaSpecific, `Chargement de l'article ID ${articleId}...`, 'info');

            fetch(`backend/get_article_by_id.php?id=${articleId}`)
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
                            if (messageAreaSpecific) messageAreaSpecific.textContent = ''; // Clear loading message
                        } else { // article-details.html
                            if (pageTitleElement) pageTitleElement.textContent = article.titre || 'Titre non disponible';
                            if (targetElement) {
                                const contenuCompletAffiche = article.contenuComplet || 'Contenu complet non disponible.';
                                const categorieAffiche = article.categorie || 'Non spécifiée';
                                const datePublicationAffiche = article.date_publication || 'Date non spécifiée';

                                // ****** C'EST LA CORRECTION ICI : LE H2 QUI CAUSAIT LE DUBLON EST SUPPRIMÉ *******
                                targetElement.innerHTML = `
                                    <figure class="article-image-figure">
                                        <img src="${article.image_url || 'placeholder.jpg'}" alt="Image de l'article : ${article.titre || 'Non disponible'}">
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
                                // ***********************************************************************************
                            }
                            if (messageAreaSpecific) messageAreaSpecific.textContent = ''; // Clear loading message
                        }
                    } else {
                        const message = "Article non trouvé ou données invalides.";
                        if (pageTitleElement) pageTitleElement.textContent = 'Article non trouvé';
                        if (targetElement) targetElement.innerHTML = `<p>${message}</p>`;
                        if (messageAreaSpecific) showMessage(messageAreaSpecific, message, 'error');
                        if (isEditPage) populateEditForm({}); // Vider les champs du formulaire d'édition
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des détails de l\'article pour affichage ou édition :', error);
                    const errorMessage = `Impossible de charger les détails de l'article. Erreur: ${error.message}`;
                    if (pageTitleElement) pageTitleElement.textContent = 'Erreur de chargement';
                    if (targetElement) targetElement.innerHTML = `<p>${errorMessage}</p>`;
                    if (messageAreaSpecific) showMessage(messageAreaSpecific, errorMessage, 'error');
                    if (isEditPage) populateEditForm({}); // Vider les champs du formulaire d'édition
                });
        } else {
            console.log('Aucun ID d\'article trouvé dans l\'URL pour détails/édition.');
            const message = 'Article non spécifié dans l\'URL.';
            if (pageTitleElement) pageTitleElement.textContent = 'Article non spécifié';
            if (targetElement) targetElement.innerHTML = `<p>${message}</p>`;
            if (messageAreaSpecific) showMessage(messageAreaSpecific, message, 'warning');
            if (isEditPage) populateEditForm({}); // Vider les champs du formulaire d'édition
        }
    }
    async function fetchArticles() {
        console.log("fetchArticles() : Fonction appelée."); // TRACE 13
        let targetContainer;
        let targetMessageArea;

        // Déterminer où afficher les articles et les messages en fonction de la page
        if (document.getElementById('articles-container')) { // Page d'accueil (index.html ou similaire)
            targetContainer = document.getElementById('articles-container');
            targetMessageArea = document.getElementById('message-area');
        } else if (document.getElementById('articlesTableBody')) { // Page d'administration (admin-articles.html)
            targetContainer = articlesTableBody;
            targetMessageArea = listMessageArea;
        } else {
            console.warn("fetchArticles(): Aucun conteneur d'articles identifiable trouvé (articles-container ou articlesTableBody).");
            return; // Sortir si aucun conteneur n'est trouvé
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
            console.log("fetchArticles() : Tentative de récupération des articles depuis :", fetchArticlesApiUrl); // TRACE 14
            const response = await fetch(fetchArticlesApiUrl);
            if (!response.ok) {
                // Tenter de lire le message d'erreur du serveur si disponible
                const errorData = await response.json().catch(() => ({ message: `Erreur HTTP: ${response.status}` }));
                throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
            }
            const data = await response.json();
            console.log("fetchArticles() : Données reçues de l'API :", data); // TRACE 15

            if (data.success && data.articles) {
                displayArticles(data.articles, targetContainer, targetMessageArea);
                // Le message de succès sera effacé par setTimeout dans showMessage
                // if (targetMessageArea) showMessage(targetMessageArea, 'Articles chargés avec succès !', 'success');
                console.log("fetchArticles() : Articles affichés avec succès."); // TRACE 16
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
                console.warn("fetchArticles() : La réponse de l'API n'indique pas le succès ou ne contient pas d'articles.", data); // TRACE WARN API
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
            console.error("Erreur de récupération des articles:", error); // TRACE ERROR FETCH
        }
    }

    function displayArticles(articles, targetContainer, targetMessageArea) {
        console.log("displayArticles() : Fonction appelée avec", articles.length, "articles."); // TRACE 17
        if (targetContainer) {
            targetContainer.innerHTML = ''; // Nettoyer le contenu existant
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

                    // S'assurer que les propriétés existent pour éviter 'undefined'
                    const imageUrl = article.image_url || 'placeholder.jpg';
                    const titre = article.titre || 'Titre non disponible';
                    const datePublication = article.date_publication || 'Date non spécifiée';
                    const resume = article.resume || 'Résumé non disponible';
                    const articleId = article.id_article || article.id || ''; // Gérer id_article ou id

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

                } else { // C'est une table (admin-articles.html)
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

            // Ajout des écouteurs d'événements de suppression si nous sommes sur la page d'administration
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
        const targetMessageArea = document.getElementById('listMessageArea') || document.getElementById('message-area'); // Utilisez la même zone de message pour la liste

        try {
            const params = new URLSearchParams();
            params.append('id', id);

            const response = await fetch(deleteArticleApiUrl, {
                method: 'POST', // La méthode POST est utilisée car DELETE peut poser des problèmes avec certains serveurs PHP si non configurés
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded' // Important pour URLSearchParams
                },
                body: params.toString() // Convertit les paramètres en chaîne pour le corps de la requête
            });

            // Vérifier si la réponse HTTP est OK (statut 200-299)
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: `Erreur HTTP: ${response.status}` }));
                throw new Error(errorData.message || `Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log("SCRIPT: deleteArticle() - Réponse de l'API de suppression :", data);

            if (data.success) {
                showMessage(targetMessageArea, "Article supprimé avec succès !", 'success');
                // Rafraîchir la liste des articles après une suppression réussie
                fetchArticles();
            } else {
                showMessage(targetMessageArea, "Erreur lors de la suppression : " + data.message, 'error');
            }

        } catch (error) {
            showMessage(targetMessageArea, "Erreur réseau ou interne lors de la suppression: " + error.message, 'error');
            console.error("SCRIPT: Erreur de suppression:", error);
        }
    }

    // L'appel initial à fetchArticles() DOIT être DANS le DOMContentLoaded
    if (document.getElementById('articles-container') || document.getElementById('articlesTableBody')) {
        console.log("DOMContentLoaded : Appel initial de fetchArticles()."); // TRACE 20
        fetchArticles();
    }

    // Ligne de débogage pour s'assurer que le script est bien la dernière version chargée
    console.log("SCRIPT.JS VERSION FINALE : " + new Date().toLocaleString());
}); // Fin de document.addEventListener('DOMContentLoaded'