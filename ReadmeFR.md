# SYSTÈME DE GESTION DE ZOO

## 1. CONTEXTE DU PROJET

Ce projet a été réalisé dans le cadre d'une SAE portant sur la conception et le développement d'un système de gestion pour un zoo fictif, **Zoo'Land** .

L'objectif du projet est de concevoir une application permettant de centraliser et de gérer les différentes activités du zoo à travers une base de données Oracle et une interface web.

Le système permet notamment de gérer :

* Les animaux et leurs espèces
* Les enclos et les différentes zones du zoo
* Les soigneurs et le suivi des soins
* L'alimentation des animaux
* Les vétérinaires et les soins complexes
* Le personnel et l'historique de leurs emplois
* Les réparations et l'entretien des enclos
* Les boutiques et leur chiffre d'affaires
* Les visiteurs et le parrainage des animaux
* Les droits et rôles des différents membres du personnel

L'interface web permet à chaque membre du personnel d'accéder uniquement aux fonctionnalités correspondant à son rôle et à ses responsabilités.

Le projet met donc l'accent sur la **gestion des données**, la **sécurité des accès**, l'**intégrité de la base de données** et la **gestion des droits utilisateurs**.

La base de données a été conçue à partir d'un Modèle Conceptuel de Données (MCD) réalisé au préalable dans le cadre de la partie Base de Données du projet .

Le MCD représente les principales entités du système ainsi que leurs relations et leurs contraintes. Il a servi de référence lors de la conception et de l'implémentation de la base de données Oracle.

<img width="1897" height="816" alt="{268A1AD8-12F5-437C-ADB4-ABECA8BE34F3}" src="https://github.com/user-attachments/assets/53781a9f-f7df-4894-b945-ce4dff9a078f" />

---


## 2. ACCÈS ET IDENTIFIANTS (TESTS PAR NIVEAUX)

Tous les comptes de test utilisent le mot de passe :

**admin1**

Les mots de passe sont stockés en base de données sous forme de hash généré avec `password_hash()`.

### A. NIVEAU : COMPTABLE (Administrateur / RH)

* Identifiant : `507` (Nina Leroy)
* Droits : Gestion complète du personnel, archivage, contrats, animaux et visualisation globale des statistiques.

### B. NIVEAU : RESPONSABLE BOUTIQUE

* Identifiant : `506` (Sophie Martin)
* Droits : Saisie et consultation du chiffre d'affaires (CA), recherche de parrains et accès à l'annuaire des visiteurs.

### C. NIVEAU : SOIGNEUR

* Identifiants : `500` (Paul Durand) / `501` (Claire Lemoine)
* Droits : Consultation de la "Tournée des soins", gestion de la nourriture et suivi médical des animaux assignés.

### D. NIVEAU : ENTRETIEN (Technique)

* Identifiant : `505` (Luc Bernard)
* Droits : Signalement de réparations et suivi de l'état des enclos.

---

## 3. EXPLICATIONS TECHNIQUES & CHOIX DE CONCEPTION

### Sécurité

L'application utilise des **sessions PHP** afin de suivre l'utilisateur connecté et de restreindre l'accès aux différentes pages et fonctionnalités.

Les droits sont vérifiés en fonction du rôle de l'utilisateur. Un utilisateur ne peut donc pas accéder aux fonctionnalités réservées à un rôle supérieur au sien.

Les mots de passe ne sont jamais stockés en clair et sont sécurisés à l'aide de `password_hash()`.

### Base de données

Le schéma de la base de données respecte les principes de normalisation afin de limiter les redondances et d'assurer la cohérence des données.

Les clés étrangères permettent notamment de maintenir l'intégrité référentielle entre les différentes tables.

Par exemple, la suppression d'un enclos contenant encore des animaux est empêchée afin d'éviter de créer des données incohérentes.

### Requêtes SQL

L'application utilise différentes requêtes SQL permettant notamment de rechercher, filtrer et croiser les informations provenant de plusieurs tables.

Les **jointures explicites (`JOIN ... ON`)** sont utilisées pour les structures complexes, tandis que des jointures implicites (`WHERE A.id = B.id`) peuvent être utilisées pour certaines associations simples.

Cette organisation permet de récupérer avec précision les informations liées au personnel, aux zones, aux animaux, aux soins et aux autres éléments du système.

### Validation des données

Les données saisies par les utilisateurs sont contrôlées afin de limiter les erreurs et les incohérences.

Une attention particulière est également portée à la sécurité des entrées utilisateur afin de limiter les risques liés notamment aux injections SQL et à l'injection de code HTML.

---

## 4. INSTALLATION ET DÉPLOIEMENT

### Base de données

1. Exécuter le fichier `bd.sql` dans l'environnement Oracle afin de créer les tables et d'insérer les jeux de données de test.

2. Configurer les paramètres de connexion à la base de données dans :

```text
myparam.inc.example.php
```

### Interface web

3. Placer le contenu du dossier `public_html` sur le serveur web.

4. Vérifier que le serveur dispose de PHP et qu'il peut accéder à la base de données Oracle.

### Réinitialisation

En cas de besoin, la base de données peut être réinitialisée en exécutant :

```text
drop.sql
```
---

## 5. FONCTIONNALITÉS PRINCIPALES

Le système permet notamment de :

* Authentifier les membres du personnel
* Gérer les sessions utilisateur
* Contrôler les droits d'accès selon les rôles
* Consulter et rechercher les informations de la base de données
* Ajouter, modifier et supprimer certaines données
* Gérer les animaux et leurs informations
* Suivre les soins réalisés sur les animaux
* Gérer l'alimentation des animaux
* Suivre les réparations des enclos
* Gérer les boutiques et leur chiffre d'affaires
* Consulter les informations relatives aux visiteurs et aux parrainages
* Consulter les statistiques selon les droits de l'utilisateur
* Gérer les informations du personnel et leur historique
  
  ## 6. DÉMONSTRATION VIDÉO

Une vidéo de démonstration du site est fournie avec le projet au format **MP4**.

Pour visualiser le fonctionnement complet de l'application et découvrir les différentes fonctionnalités disponibles selon les rôles, il suffit d'ouvrir le fichier :

```text
ZOOLAND.mp4
```

**Durée : 6 min 13 s**

La vidéo présente notamment la connexion des différents utilisateurs, la gestion des droits, la consultation des données ainsi que les principales fonctionnalités de l'application.

