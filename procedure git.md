# 🚀 Tutoriel Git pour le Projet "Catalogue"

Ce guide exhaustif vous accompagnera à travers toutes les étapes pour initier Git dans votre projet "catalogue" (hébergé localement avec XAMPP) et vous fournira les clés de son utilisation quotidienne pour une gestion de version efficace, incluant la sauvegarde sur GitHub.

---

## **PARTIE 1 : MISE EN PLACE DE GIT DANS LE PROJET "CATALOGUE"**

Cette section détaille la configuration initiale de Git pour votre projet, depuis l'initialisation du dépôt local jusqu'à sa première synchronisation avec votre dépôt distant sur GitHub.

---

### **1.1. Prérequis : Vérifier l'installation de Git**

Avant de commencer, assurez-vous que Git est correctement installé sur votre système.

1.  Ouvrez votre **Invite de commande** (sur Windows) ou votre **Terminal** (sur macOS/Linux).
2.  Saisissez la commande suivante et appuyez sur Entrée :

    ```bash
    git --version
    ```

3.  **Attendu :** Vous devriez voir la version de Git installée (par exemple : `git version 2.40.0.windows.1`). Si un message d'erreur survient (`'git' n'est pas reconnu...`), Git n'est pas installé ou n'est pas configuré dans votre PATH.

---

### **1.2. Naviguer vers le dossier racine du projet**

Pour que Git puisse opérer sur votre code, vous devez positionner votre terminal dans le dossier principal de votre projet.

1.  Dans votre Invite de commande/Terminal, utilisez la commande `cd` (change directory) pour vous déplacer jusqu'à votre dossier `catalogue`.
    * **Exemple de chemin typique pour XAMPP sous Windows :**
        ```bash
        cd C:\xampp\htdocs\catalogue
        ```
    * Appuyez sur Entrée.

2.  **Confirmation :** L'invite de commande devrait désormais afficher le chemin de votre projet (par exemple : `C:\xampp\htdocs\catalogue>`).

---

### **1.3. Initialiser le dépôt Git local**

C'est l'action fondamentale qui signale à Git de commencer le suivi des modifications de votre projet dans ce répertoire.

1.  Depuis le dossier de votre projet dans le terminal (`C:\xampp\htdocs\catalogue>`), exécutez la commande :

    ```bash
    git init
    ```
    Appuyez sur Entrée.

2.  **Confirmation :** Le message suivant devrait apparaître : `Initialized empty Git repository in C:/xampp/htdocs/catalogue/.git/`
    * Un dossier caché nommé `.git` est créé à la racine de votre projet. C'est le cœur de votre dépôt Git local.

---

### **1.4. Configurer votre identité Git (Configuration Initiale Globale)**

Git enregistre l'auteur de chaque modification (commit). Cette étape est généralement effectuée une seule fois par machine pour tous vos dépôts.

1.  Dans le même terminal, saisissez les deux commandes suivantes, en **remplaçant les valeurs entre guillemets** par votre nom et votre adresse e-mail réels :

    ```bash
    git config --global user.name "Votre Nom Complet"
    ```
    ```bash
    git config --global user.email "votre.email@example.com"
    ```
    Appuyez sur Entrée après chaque commande.

2.  **Note :** Ces commandes ne retournent habituellement pas de message de succès visible, ce qui est le comportement attendu.

---

### **1.5. Créer le fichier `.gitignore`**

Ce fichier essentiel instruit Git sur les fichiers et dossiers à ignorer, empêchant ainsi le suivi de fichiers temporaires, de cache, de configurations sensibles ou de dépendances régénérables.

1.  Ouvrez un **éditeur de texte** (fortement recommandé : **VS Code**, Notepad++, Sublime Text).
2.  Copiez et collez le contenu suivant dans le nouvel onglet de votre éditeur :

    ```
    # --- Fichiers et dossiers à ignorer par Git ---

    # Ignorer les fichiers de configuration spécifiques à l'environnement local
    # TRÈS IMPORTANT : Adaptez ce nom si votre fichier de config de BDD est différent !
    config.local.php
    db_config.php 

    # Ignorer les dépendances PHP (générées par Composer)
    /vendor/

    # Ignorer les fichiers de log ou de cache générés par l'application
    *.log
    *.cache

    # Ignorer les fichiers de session temporaires créés par PHP
    /tmp/

    # Ignorer les fichiers de base de données SQLite (si vous en utilisez)
    *.sqlite
    *.db

    # Ignorer les fichiers spécifiques aux EDI (Environnements de Développement Intégrés) ou au système d'exploitation
    .idea/          # Configuration pour JetBrains IDEs (IntelliJ, PhpStorm, WebStorm...)
    .vscode/        # Configuration pour VS Code
    .DS_Store       # Fichiers de métadonnées macOS
    Thumbs.db       # Fichiers de cache de miniatures Windows
    ehthumbs.db     # Fichiers de cache de miniatures Windows
    *.bak           # Fichiers de sauvegarde générés automatiquement
    desktop.ini     # Fichier de configuration de dossier Windows

    # OPTIONNEL : Ignorer les dossiers d'upload (décommenter si vous ne voulez PAS versionner les fichiers téléchargés par les utilisateurs)
    # /uploads/ 

    # Ignorer les dossiers de modules Node.js (si vous utilisez npm pour le frontend)
    /node_modules/

    # Ignorer les fichiers de debug ou de swap
    *.swp
    *.swo
    ```

3.  **Enregistrez le fichier :**
    * Allez dans le menu `Fichier` > `Enregistrer sous...`
    * Naviguez jusqu'à la **racine de votre dossier de projet** (`C:\xampp\htdocs\catalogue`).
    * Dans le champ "Nom du fichier", tapez **exactement** : `.gitignore`
    * Dans le champ "Type" ou "Type de fichier", sélectionnez **"Tous les fichiers"** (ou "All Files") pour garantir qu'aucune extension (`.txt` par exemple) ne soit ajoutée automatiquement.
    * Cliquez sur "Enregistrer".

---

### **1.6. Ajouter tous les fichiers à la zone de préparation (staging area)**

Cette action indique à Git quels fichiers (modifiés, ajoutés, supprimés) vous souhaitez inclure dans votre prochain enregistrement (commit).

1.  Dans votre terminal (vérifiez bien que vous êtes toujours dans `C:\xampp\htdocs\catalogue>`), tapez :

    ```bash
    git add .
    ```
    Appuyez sur Entrée.

2.  **Note :** Cette commande n'affiche généralement pas de message de confirmation si elle réussit. Vous pourriez voir un avertissement concernant les fins de ligne (`LF will be replaced by CRLF`), qui est normal sous Windows et peut être ignoré.

---

### **1.7. Effectuer le premier commit local (Votre Premier Point Nemo)**

Ceci crée le tout premier "snapshot" (instantané) de l'état actuel de votre projet et l'enregistre de manière permanente dans l'historique de votre dépôt Git local.

1.  Dans votre terminal, exécutez la commande suivante :

    ```bash
    git commit -m "Initial commit: Projet d'administration des articles stable et fonctionnel"
    ```
    Appuyez sur Entrée.

2.  **Confirmation :** Vous verrez un résumé détaillé du commit, incluant le nombre de fichiers modifiés et insérés, ainsi que le hash de votre commit :
    ```
    [master (root-commit) 9da4573] Initial commit: Projet d'administration des articles stable et fonctionnel
     57 files changed, 4500 insertions(+)
     create mode 100644 .gitignore
     ... (liste complète de tous vos fichiers) ...
    ```
    Votre projet est maintenant versionné localement !

---

### **1.8. Créer un dépôt distant sur GitHub**

Pour une sauvegarde en ligne robuste et la possibilité de collaboration/accès multi-appareil, créez un dépôt sur GitHub.

1.  Ouvrez votre navigateur web et accédez à [github.com](https://github.com/).
2.  Connectez-vous à votre compte GitHub (créez-en un si ce n'est pas déjà fait).
3.  Sur la page d'accueil de GitHub, cliquez sur le bouton **"New repository"** (ou le signe `+` en haut à droite, puis "New repository").
4.  Sur la page "Create a new repository", renseignez les informations suivantes :
    * **Repository name :** `catalogue` (il est fortement recommandé d'utiliser le même nom que votre dossier de projet local).
    * **Description :** (Optionnel, mais utile) Une brève explication de votre projet.
    * **Public ou Private :** Choisissez "Public" pour que votre code soit visible par tous (idéal pour un portfolio) ou "Private" pour un accès restreint.
    * **Initialize this repository with :** **NE COCHEZ AUCUNE DES CASES** (`Add a README file`, `Add .gitignore`, `Choose a license`). Nous avons déjà notre dépôt local et notre `.gitignore`.
5.  Cliquez sur le bouton vert **"Create repository"**.
6.  GitHub affichera alors une page avec des instructions pour lier un dépôt existant. **Gardez cette page ouverte**, car nous allons utiliser les commandes qu'elle contient.

---

### **1.9. Lier le dépôt local à GitHub et pousser les fichiers**

Cette étape finale de la mise en place envoie tous les commits de votre dépôt local vers votre nouveau dépôt sur GitHub.

1.  Assurez-vous que votre terminal est toujours positionné dans le dossier racine de votre projet (`C:\xampp\htdocs\catalogue>`).

2.  **Copiez la PREMIÈRE commande fournie par GitHub** sur la page des instructions (elle commence par `git remote add origin...`). Collez-la dans votre terminal et appuyez sur Entrée.
    * **Exemple :**
        ```bash
        git remote add origin [https://github.com/VotreNomUtilisateur/catalogue.git](https://github.com/VotreNomUtilisateur/catalogue.git)
        ```
    * **Rôle :** Cette commande établit un lien entre votre dépôt local et le dépôt distant sur GitHub, en lui donnant le nom `origin`.

3.  **Copiez la DEUXIÈME commande fournie par GitHub** (elle commence généralement par `git branch -M main`). Collez-la dans votre terminal et appuyez sur Entrée.
    * **Exemple :**
        ```bash
        git branch -M main
        ```
    * **Rôle :** Cette commande garantit que votre branche locale principale est nommée `main`, ce qui correspond à la convention moderne de GitHub.

4.  **Copiez la TROISIÈME et dernière commande fournie par GitHub** (elle commence par `git push -u origin main`). Collez-la dans votre terminal et appuyez sur Entrée.
    * **Exemple :**
        ```bash
        git push -u origin main
        ```
    * **Rôle :**
        * `git push` : Envoie vos commits locaux vers le dépôt distant.
        * `-u origin main` : Définit la branche locale `main` pour qu'elle "suive" la branche `main` sur le dépôt `origin`. Pour les prochains pushes et pulls, vous pourrez simplement utiliser `git push` ou `git pull`.
    * **Authentification :** Une fenêtre de navigateur peut s'ouvrir pour que vous vous connectiez à GitHub et autoriserez l'opération. Suivez les instructions.

5.  **Confirmation du succès :** Vous verrez une progression du téléversement et un message final comme :
    ```
    Enumerating objects: ..., done.
    ...
    To [https://github.com/Millot-2023/catalogue.git](https://github.com/Millot-2023/catalogue.git)
     * [new branch]      main -> main
    branch 'main' set up to track 'origin/main'.
    ```

6.  **Vérification Finale :** Rafraîchissez la page de votre dépôt sur GitHub dans votre navigateur. Vous devriez voir tous vos fichiers et dossiers de projet apparaître, ainsi que l'historique de votre "Initial commit".

---

## **PARTIE 2 : UTILISATION QUOTIDIENNE DE GIT**

Une fois la mise en place initiale effectuée, voici le flux de travail de base que vous suivrez régulièrement pour gérer vos modifications avec Git.

---

### **2.1. Le Cycle de Développement et de Versionnement**

Le processus se résume à un cycle répétitif : **Modifier le code → Observer les changements → Préparer les changements → Enregistrer les changements → Sauvegarder en ligne.**

#### **a. Modifier le code localement**

* C'est l'étape où vous travaillez activement sur votre projet : ajoutez de nouvelles fonctionnalités, corrigez des bugs, affinez le style CSS, etc.
* Vous travaillez dans votre éditeur de code, directement sur les fichiers du dossier `C:\xampp\htdocs\catalogue`.

#### **b. Vérifier le statut des fichiers : `git status`**

Après avoir apporté des modifications, cette commande vous informe de l'état actuel de votre répertoire de travail par rapport à votre dernier commit.

```bash
git status
```

**Utilité :** Elle vous montrera :
* Les fichiers `modified` (modifiés depuis le dernier commit).
* Les fichiers `untracked` (nouveaux fichiers qui ne sont pas encore suivis par Git).
* Les fichiers `staged` (ceux que vous avez ajoutés pour le prochain commit).

Cela vous aide à avoir une vue d'ensemble de ce que vous avez changé.

#### **c. Préparer les changements pour le commit : `git add`**

Cette étape consiste à sélectionner les modifications spécifiques que vous souhaitez inclure dans votre prochain "snapshot" (commit).

*Pour ajouter **tous** les changements (modifiés, ajoutés, supprimés) dans le répertoire courant et ses sous-répertoires :*
```bash
git add .
```
*Pour ajouter un fichier ou un dossier spécifique :*
```bash
git add nom/du/fichier.html
```
```bash
git add dossier/images/
```
**Utilité :** Cela vous permet de regrouper logiquement vos changements en "unités de travail" cohérentes avant de les enregistrer.

#### **d. Enregistrer les changements localement (commit) : `git commit`**

Une fois que vos modifications sont préparées (via `git add`), cette commande les enregistre de manière permanente dans l'historique de votre dépôt Git local.

```bash
git commit -m "Message descriptif de vos modifications"
```

**L'importance du message :** Le message de commit est crucial ! Il doit être clair, concis et décrire précisément **ce que ce commit apporte** et **pourquoi**.
* **Exemples de bons messages :**
    * `feat: Ajout de la page 'À propos' avec section contact`
    * `fix: Correction du bug d'affichage des articles sur mobile`
    * `refactor: Optimisation du code PHP dans get_articles.php`
    * `docs: Mise à jour du README avec les instructions d'installation`

Chaque commit crée un nouveau point récupérable dans l'historique de votre projet.

#### **e. Sauvegarder vos changements sur GitHub : `git push`**

Après avoir effectué un ou plusieurs commits locaux, cette commande envoie ces commits vers votre dépôt distant sur GitHub, assurant ainsi la sauvegarde et la synchronisation de votre travail.

```bash
git push
```

**Utilité :** C'est votre sauvegarde en ligne. Non seulement cela protège votre travail en cas de problème local, mais cela vous permet aussi de partager vos avancées et de collaborer si vous travaillez en équipe.

### **2.2. Récupérer les changements depuis GitHub (si applicable)**

Si vous travaillez sur votre projet depuis plusieurs machines, ou si vous collaborez avec d'autres personnes, vous devrez parfois récupérer les modifications qui ont été poussées sur GitHub par d'autres.

*Pour télécharger et fusionner les dernières modifications depuis GitHub vers votre dépôt local :*
```bash
git pull
```

**Utilité :** Assurez-vous d'avoir toujours la version la plus à jour de votre code localement.

---

## **3. Que faire quand j'ouvre mon projet en localhost depuis XAMPP pour que Git soit opérationnel ?**

Excellente question, et la réponse est simple : **Vous n'avez rien de spécifique à faire dans XAMPP ou votre navigateur pour que Git soit "opérationnel".**

* **XAMPP et localhost :** Ils servent uniquement à **exécuter et visualiser** votre application web (vos fichiers HTML, CSS, JavaScript, et l'exécution de votre PHP/MySQL) dans votre navigateur. Ils ne gèrent absolument pas la version du code.
* **Git :** Git est un **outil en ligne de commande** (ou via une interface graphique Git) qui travaille directement sur les fichiers de votre système de fichiers.

**Pour utiliser Git (le rendre "opérationnel") sur votre projet :**

1.  **Ouvrez simplement votre Invite de commande/Terminal.**
2.  **Naviguez jusqu'au dossier racine de votre projet `catalogue`** (`C:\xampp\htdocs\catalogue`).

Une fois que vous êtes dans ce dossier dans le terminal, Git est prêt à l'emploi. Vous pouvez alors exécuter toutes les commandes Git (`git status`, `git add`, `git commit`, `git push`, etc.) pour gérer et sauvegarder les versions de votre code.

Vous continuerez à développer et tester via votre navigateur avec XAMPP, mais pour **versionner et sauvegarder votre travail**, vous utiliserez toujours le terminal.