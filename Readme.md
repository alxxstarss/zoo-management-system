===========================================================

PROJET : SYSTÈME DE GESTION DE ZOO (SAE)

===========================================================


\-----------------------------------------------------------

1\. ACCÈS ET IDENTIFIANTS (TESTS PAR NIVEAUX)

\-----------------------------------------------------------

Tous les comptes de test utilisent le mot de passe : **admin1**

Les mots de passe en base de données sont sécurisés via password\_hash().



A. NIVEAU : COMPTABLE (Administrateur / RH)

&#x20;  - Identifiant : 507 (Nina Leroy)

&#x20;  - Droits : Gestion complète du personnel, archivage, contrats, 

&#x20;    animaux et visualisation globale des statistiques.



B. NIVEAU : RESPONSABLE BOUTIQUE

&#x20;  - Identifiant : 506 (Sophie Martin)

&#x20;  - Droits : Saisie et consultation du Chiffre d'Affaires (CA), 

&#x20;    recherche de parrains et accès à l'annuaire des visiteurs.



C. NIVEAU : SOIGNEUR

&#x20;  - Identifiants : 500 (Paul Durand) / 501 (Claire Lemoine)

&#x20;  - Droits : Consultation de la "Tournée des soins", gestion de 

&#x20;    la nourriture et suivi médical des animaux assignés.



D. NIVEAU : ENTRETIEN (Technique)

&#x20;  - Identifiant : 505 (Luc Bernard)

&#x20;  - Droits : Signalement de réparations et suivi de l'état des enclos.



\-----------------------------------------------------------

2\. EXPLICATIONS TECHNIQUES \& CHOIX DE CONCEPTION

\-----------------------------------------------------------

\- Sécurité : Utilisation de sessions PHP pour restreindre l'accès aux 

&#x20; pages. Un utilisateur ne peut pas accéder aux fonctions d'un rôle 

&#x20; supérieur au sien.

\- Base de données : Le schéma respecte les formes normales pour éviter 

&#x20; la redondance. Les clés étrangères assurent l'intégrité référentielle 

&#x20; (ex: suppression d'un enclos impossible s'il contient des animaux).

\- Requêtes SQL : Utilisation de JOIN (jointures internes) pour optimiser 

&#x20; Optimisation de la récupération des données via l'utilisation de jointures

&#x20; explicites (JOIN...ON) pour les structures complexes et de jointures implicites 

&#x20; (WHERE A.id = B.id) pour les associations simples. Cette approche garantit la 

&#x20; précision des résultats lors du croisement des données entre le personnel, les 

&#x20; zones et les animaux.



\-----------------------------------------------------------

3\. INSTALLATION ET DÉPLOIEMENT

\-----------------------------------------------------------

1\. Exécuter le fichier 'bd.sql' sur environnement Oracle pour 

&#x20;  créer les tables et insérer les jeux de données de test.

2\. Placer le contenu du dossier 'public\_html' sur votre serveur web.

3\. Configurer les identifiants (Host, User, Pass) dans 'myparam.inc.php'.

4\. En cas de réinitialisation, exécuter 'drop.sql'.



\-----------------------------------------------------------

4\. DÉMONSTRATION VIDÉO

\-----------------------------------------------------------

Fichier : ZOOLAND.mp4 (Durée : 6:13 minutes)

La vidéo présente le workflow complet, de la connexion d'un soigneur 

à la modification des données par un administrateur.

===========================================================

