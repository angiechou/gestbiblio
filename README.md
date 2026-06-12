# gestbiblio

1. Contexte et objectif du projet
GESTBIBLIO est une application web développée dans le cadre de l'évaluation académique HECM JERICHO 2026 (SIL 2). Elle vise à centraliser et automatiser la gestion des emprunts de livres au sein de la bibliothèque universitaire de la Haute École de Commerce et de Management (HECM).
L'application remplace un processus manuel peu fiable par un système numérique sécurisé permettant à l'administration de gérer le catalogue et les demandes d'emprunt, et aux étudiants de suivre leurs emprunts en temps réel.

2. Fonctionnalités développées
Authentification
    • Connexion sécurisée (email ou username + mot de passe)
    • Inscription avec hachage automatique du mot de passe (password_hash)
    • Redirection dynamique selon le rôle : admin → admin/dashboard.php · user → user/dashboard.php

Espace Administrateur
    • Tableau de bord : statistiques globales (livres enregistrés, emprunts 
    en attente, livres sortis)
    • Ajout de livres : formulaire titre + auteur, stock initialisé à 1
    • Catalogue : gestion des stocks, retrait sécurisé (bloqué si livre en cours d'emprunt)
    • Demandes : validation des emprunts avec date de retour J+14, mise à jour du stock

Espace Utilisateur (Étudiant)
    • Tableau de bord : suivi de ses propres emprunts et statuts en temps réel
    • Catalogue : consultation de tous les livres avec bouton dynamique (Demander / Déjà emprunté / Indisponible)
    • Demande d'emprunt : soumission instantanée avec redirection et message de succès

Gestion du Profil
    • Modification du username et de l'email
    • Changement de mot de passe avec vérification
    • Upload de photo de profil (extensions jpg/png uniquement, renommage sécurisé côté serveur)

Stack technologique : PHP 8 · MySQL · PDO (requêtes préparées) · HTML5 · CSS3 · aucun framework externe.

Bonne utilisation !
L'équipe projet.