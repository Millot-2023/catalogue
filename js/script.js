// script.js

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


    // --- Début du code de gestion des articles ---
    const articleForm = document.getElementById('articleForm');
    const messageArea = document.getElementById('messageArea');

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

            const apiUrl = 'http://localhost/catalogue/backend/add_article.php';

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


    const articlesTableBody = document.getElementById('articlesTableBody');
    const listMessageArea = document.getElementById('listMessageArea');

    console.log("Articles : Éléments de la liste d'articles récupérés."); // TRACE 12

    const fetchArticlesApiUrl = 'http://localhost/catalogue/backend/get_articles.php';
    const deleteArticleApiUrl = 'http://localhost/catalogue/backend/delete_article.php';

    async function fetchArticles() {
        console.log("fetchArticles() : Fonction appelée."); // TRACE 13
        if (listMessageArea) {
            listMessageArea.textContent = 'Chargement des articles...';
            listMessageArea.style.color = '#888';
        }
        if (articlesTableBody) {
            articlesTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Chargement des articles...</td></tr>';
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
                displayArticles(data.articles);
                if (listMessageArea) listMessageArea.textContent = '';
                console.log("fetchArticles() : Articles affichés avec succès."); // TRACE 16
            } else {
                if (listMessageArea) {
                    listMessageArea.textContent = "Erreur lors du chargement des articles : " + (data.message || "Aucun article trouvé.");
                    listMessageArea.style.color = 'red';
                }
                if (articlesTableBody) {
                    articlesTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">' + (data.message || "Aucun article trouvé.") + '</td></tr>';
                }
                console.warn("fetchArticles() : La réponse de l'API n'indique pas le succès ou ne contient pas d'articles.", data); // TRACE WARN API
            }

        } catch (error) {
            if (listMessageArea) {
                listMessageArea.textContent = "Impossible de charger les articles. Erreur: " + error.message;
                listMessageArea.style.color = 'red';
            }
            if (articlesTableBody) {
                articlesTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Erreur de chargement.</td></tr>';
            }
            console.error("Erreur de récupération des articles:", error); // TRACE ERROR FETCH
        }
    }

    function displayArticles(articles) {
        console.log("displayArticles() : Fonction appelée avec", articles.length, "articles."); // TRACE 17
        if (articlesTableBody) {
            articlesTableBody.innerHTML = '';
            if (articles.length === 0) {
                articlesTableBody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Aucun article à afficher.</td></tr>';
                return;
            }

            articles.forEach(article => {
                const row = articlesTableBody.insertRow();
                row.innerHTML = `
                    <td>${article.id}</td>
                    <td>${article.titre}</td>
                    <td><img src="${article.image_url}" alt="Image" style="width: 50px; height: auto;"></td>
                    <td>${article.date_publication}</td>
                    <td>${article.resume.substring(0, 70)}...</td>
                    <td class="actions-cell">
                        <a href="edit-article.html?id=${article.id}" class="btn btn-edit">Modifier</a>
                        <button class="btn btn-delete" data-id="${article.id}">Supprimer</button>
                    </td>
                `;
            });

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
    console.log("DOMContentLoaded : Appel initial de fetchArticles()."); // TRACE 20
    fetchArticles();
    // --- Fin du code de gestion des articles ---

}); // Fin de document.addEventListener('DOMContentLoaded'