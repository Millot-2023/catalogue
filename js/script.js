// Attendre que le DOM soit entièrement chargé avant d'exécuter le script
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOMContentLoaded : Le DOM est entièrement chargé."); // TRACE 1

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
    const messageArea = document.getElementById('messageArea'); // Pour admin-articles.html

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

            // *** CORRECTION ICI : Utilisation d'un chemin relatif pour le développement local ***
            const apiUrl = 'backend/add_article.php';

            try {
                console.log("Articles : Tentative d'envoi de l'article à l'API."); // TRACE 9
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(articleData)
                });

                const result = await response.json();
                console.log("Articles : Réponse de l'API d'ajout d'article reçue :", result); // TRACE 10

                messageArea.textContent = result.message;
                if (result.success) {
                    messageArea.style.color = 'green';
                    articleForm.reset();
                    console.log("Articles : Article ajouté avec succès. Appel de fetchArticles()."); // TRACE 11
                    fetchArticles();
                } else {
                    messageArea.style.color = 'red';
                }

            } catch (error) {
                messageArea.textContent = "Erreur réseau ou interne lors de l'ajout : " + error.message;
                messageArea.style.color = 'red';
                console.error('Erreur Fetch lors de l\'ajout:', error); // TRACE ERROR ADD
            }
        });
    }

    const articlesTableBody = document.getElementById('articlesTableBody'); // Pour admin-articles.html
    const listMessageArea = document.getElementById('listMessageArea'); // Pour admin-articles.html

    console.log("Articles : Éléments de la liste d'articles récupérés."); // TRACE 12

    // *** CORRECTION ICI : Utilisation de chemins relatifs pour le développement local ***
    const fetchArticlesApiUrl = 'backend/get_articles.php';
    const deleteArticleApiUrl = 'backend/delete_article.php';

    // --- DÉBUT DE LA FONCTION getArticleDetails DÉPLACÉE ET MODIFIÉE ---
    // Fonction pour charger les détails d'un article spécifique
    function getArticleDetails(id) {
        // AJOUT DE LA TRACE DE DÉBUG DÈS LE DÉBUT DE LA FONCTION
        console.log("getArticleDetails() : Tentative de récupération pour l'ID:", id);

        const articleContentDiv = document.getElementById('article-detail-content');
        if (!articleContentDiv) {
            console.error('L\'élément #article-detail-content est introuvable.');
            return;
        }

        articleContentDiv.innerHTML = '<p>Chargement des détails de l\'article...</p>'; // Message de chargement

        // *** CORRECTION ICI (si ce n'était pas déjà fait) : Utilisation d'un chemin relatif ***
        fetch(`backend/get_article_by_id.php?id=${id}`)
            .then(response => {
                // AJOUT DE LA TRACE POUR VOIR LA RÉPONSE HTTP BRUTE
                console.log("getArticleDetails() : Réponse HTTP reçue, statut:", response.status);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(article => {
                // AJOUT DE LA TRACE POUR VOIR L'OBJET ARTICLE REÇU
                console.log("getArticleDetails() : Données de l'article reçues :", article);

                // IMPORTANT : S'assurer que les clés de l'objet 'article' correspondent EXACTEMENT aux clés renvoyées par votre PHP.
                // D'après votre console, vous avez : id_article, titre, image_url, date_publication, resume, contenuComplet.
                if (article && article.id_article) { // Vérifie si l'article est trouvé et a la propriété id_article
                    // Mise à jour du titre principal de la page si un élément avec l'ID 'article-title-display' existe
                    const pageTitleElement = document.getElementById('article-title-display');
                    if (pageTitleElement) {
                        pageTitleElement.textContent = article.titre || 'Titre non disponible';
                    }

                    // Utilisation de `article.contenuComplet` comme indiqué par votre console log
                    // Ajout de fallback pour les valeurs qui pourraient être undefined
                    const contenuCompletAffiche = article.contenuComplet || 'Contenu complet non disponible.';
                    const categorieAffiche = article.categorie || 'Non spécifiée'; // `categorie` n'apparaissait pas dans votre console log, donc ajout d'un fallback solide.
                    const datePublicationAffiche = article.date_publication || 'Date non spécifiée';


                    articleContentDiv.innerHTML = `
                        <h2>${article.titre || 'Titre non disponible'}</h2>
                        <img src="${article.image_url || 'placeholder.jpg'}" alt="Image de l'article : ${article.titre || 'Non disponible'}" style="max-width: 100%; height: auto; margin-bottom: 20px;">
                        <p><strong>Résumé :</strong> ${article.resume || 'Résumé non disponible'}</p>
                        <div>
                            <h3>Contenu Complet :</h3>
                            <p>${contenuCompletAffiche}</p>
                        </div>
                        <p>Catégorie : ${categorieAffiche}</p>
                        <p>Date de publication : ${datePublicationAffiche}</p>
                    `;
                } else {
                    const pageTitleElement = document.getElementById('article-title-display');
                    if (pageTitleElement) {
                        pageTitleElement.textContent = 'Article non trouvé';
                    }
                    articleContentDiv.innerHTML = '<p>Article non trouvé.</p>';
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des détails de l\'article :', error);
                const pageTitleElement = document.getElementById('article-title-display');
                if (pageTitleElement) {
                    pageTitleElement.textContent = 'Erreur de chargement';
                }
                articleContentDiv.innerHTML = `<p>Impossible de charger les détails de l'article. Erreur: ${error.message}</p>`;
            });
    }
    // --- FIN DE LA FONCTION getArticleDetails DÉPLACÉE ET MODIFIÉE ---


    async function fetchArticles() {
        console.log("fetchArticles() : Fonction appelée."); // TRACE 13
        // Cette section a été réorganisée pour être compatible avec les deux pages (articles.html et admin-articles.html)
        let targetContainer;
        let targetMessageArea;

        // Détecte si on est sur la page articles.html ou admin-articles.html
        if (document.getElementById('articles-container')) { // Si l'élément de la page publique existe
            targetContainer = document.getElementById('articles-container');
            targetMessageArea = document.getElementById('message-area'); // Zone de message pour articles.html
        } else { // Sinon, on est sur la page d'administration
            targetContainer = articlesTableBody;
            targetMessageArea = listMessageArea; // Zone de message pour admin-articles.html
        }

        if (targetMessageArea) {
            targetMessageArea.textContent = 'Chargement des articles...';
            targetMessageArea.style.color = '#888';
        }
        if (targetContainer) {
            // Pour articles.html, on remplace le contenu du div, pour admin-articles.html, on remplace le tbody
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
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            const data = await response.json();
            console.log("fetchArticles() : Données reçues de l'API :", data); // TRACE 15

            if (data.success && data.articles) {
                // Appel de displayArticles en passant le container et la zone de message appropriés
                displayArticles(data.articles, targetContainer, targetMessageArea);
                if (targetMessageArea) targetMessageArea.textContent = '';
                console.log("fetchArticles() : Articles affichés avec succès."); // TRACE 16
            } else {
                const message = data.message || "Aucun article trouvé.";
                if (targetMessageArea) {
                    targetMessageArea.textContent = "Erreur lors du chargement des articles : " + message;
                    targetMessageArea.style.color = 'red';
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
                targetMessageArea.textContent = errorMessage;
                targetMessageArea.style.color = 'red';
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

    // MODIFICATION DE LA SIGNATURE : Ajout de targetContainer et targetMessageArea
    function displayArticles(articles, targetContainer, targetMessageArea) {
        console.log("displayArticles() : Fonction appelée avec", articles.length, "articles."); // TRACE 17
        if (targetContainer) {
            targetContainer.innerHTML = ''; // Vide le contenu actuel
            if (articles.length === 0) {
                const noArticlesMessage = '<p style="text-align: center;">Aucun article à afficher pour le moment.</p>';
                if (targetContainer.id === 'articles-container') {
                    targetContainer.innerHTML = noArticlesMessage;
                } else {
                    targetContainer.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun article à afficher.</td></tr>';
                }
                return;
            }

            articles.forEach(article => {
                if (targetContainer.id === 'articles-container') {
                    // Logique pour articles.html (affichage en cards)
                    const articleCard = document.createElement('article');
                    articleCard.classList.add('article-card');

                    // Notez l'utilisation de `article.id_article` si votre get_articles.php renvoie `id_article` et non `id`
                    articleCard.innerHTML = `
                        <div class="article-image-container">
                            <img src="${article.image_url}" alt="Image de l'article : ${article.titre}">
                        </div>
                        <div class="card-content">
                            <h3>${article.titre}</h3>
                            <p class="article-date">Publié le: ${article.date_publication}</p>
                            <p>${article.resume}</p>
                            <div class="card-button-wrapper">
                                <a href="article-details.html?id=${article.id_article || article.id}" class="read-more-btn">Lire la suite</a>
                            </div>
                        </div>
                    `;
                    // --- AJOUT DE LA LIGNE DE DÉBOGAGE ICI ---
                    console.log("GENERATION DU LIEN - ID:", (article.id_article || article.id), "TITRE:", article.titre);
                    // -----------------------------------------
                    targetContainer.appendChild(articleCard);

                } else {
                    // Logique pour admin-articles.html (affichage en tableau)
                    const row = targetContainer.insertRow();
                    row.innerHTML = `
                        <td>${article.id_article || article.id}</td>
                        <td>${article.titre}</td>
                        <td><img src="${article.image_url}" alt="Image" style="width: 50px; height: auto;"></td>
                        <td>${article.date_publication}</td>
                        <td>${article.resume.substring(0, 70)}...</td>
                        <td class="actions-cell">
                            <a href="edit-article.html?id=${article.id_article || article.id}" class="btn btn-edit">Modifier</a>
                            <button class="btn btn-delete" data-id="${article.id_article || article.id}">Supprimer</button>
                        </td>
                    `;
                }
            });

            // Ajout des écouteurs de suppression uniquement pour admin-articles.html
            if (targetContainer.id !== 'articles-container') {
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
        console.log("deleteArticle() : Tentative de suppression de l'article ID:", id); // TRACE 18
        try {
            const formData = new FormData();
            formData.append('id', id);

            const response = await fetch(deleteArticleApiUrl, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }

            const data = await response.json();
            console.log("deleteArticle() : Réponse de l'API de suppression :", data); // TRACE 19

            if (data.success) {
                alert("Article supprimé avec succès !");
                fetchArticles();
            } else {
                alert("Erreur lors de la suppression : " + data.message);
            }

        } catch (error) {
            alert("Erreur réseau ou interne lors de la suppression: " + error.message);
            console.error("Erreur de suppression:", error); // TRACE ERROR DELETE
        }
    }

    // L'appel initial à fetchArticles() DOIT être DANS le DOMContentLoaded
    // Il doit être conditionnel pour ne s'exécuter que sur les pages concernées
    if (document.getElementById('articles-container') || document.getElementById('articlesTableBody')) {
        console.log("DOMContentLoaded : Appel initial de fetchArticles()."); // TRACE 20
        fetchArticles();
    }

    // --- Code pour article-details.html ---
    // Vérifie si nous sommes sur la page article-details.html
    if (window.location.pathname.includes('article-details.html')) {
        const urlParams = new URLSearchParams(window.location.search);
        const articleId = urlParams.get('id');

        if (articleId) {
            console.log('ID de l\'article à afficher :', articleId);
            // AJOUT DE LA TRACE APRÈS L'APPEL POUR CONFIRMER L'EXÉCUTION
            console.log("Appel de getArticleDetails effectué pour l'ID:", articleId);
            // Appel de la fonction pour charger les détails de cet article
            getArticleDetails(articleId);
        } else {
            console.log('Aucun ID d\'article trouvé dans l\'URL.');
            document.getElementById('article-detail-content').innerHTML = '<p>Article non spécifié.</p>';
        }
    }

    // Ligne de débogage pour s'assurer que le script est bien la dernière version chargée
    console.log("SCRIPT.JS VERSION FINALE : " + new Date().toLocaleString());
}); // Fin de document.addEventListener('DOMContentLoaded'